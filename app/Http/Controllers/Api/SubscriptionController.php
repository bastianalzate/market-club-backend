<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Obtener la suscripción actual del usuario
     */
    public function getCurrentSubscription()
    {
        try {
            $user = Auth::user();
            $subscription = $user->activeSubscription;

            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No tienes una suscripción activa'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $subscription->id,
                    'plan' => [
                        'id' => $subscription->subscriptionPlan->id,
                        'name' => $subscription->subscriptionPlan->name,
                        'slug' => $subscription->subscriptionPlan->slug,
                        'price' => $subscription->subscriptionPlan->price,
                        'description' => $subscription->subscriptionPlan->description,
                    ],
                    'status' => $subscription->status,
                    'price_paid' => $subscription->price_paid,
                    'starts_at' => $subscription->starts_at->format('Y-m-d'),
                    'ends_at' => $subscription->ends_at->format('Y-m-d'),
                    'next_billing_date' => $subscription->next_billing_date->format('Y-m-d'),
                    'auto_renew' => $subscription->auto_renew,
                    'days_remaining' => $subscription->days_remaining,
                    'payment_method' => $subscription->masked_payment_method,
                    'is_active' => $subscription->isActive(),
                    'is_expired' => $subscription->isExpired(),
                    'is_suspended' => $subscription->isSuspended(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting current subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la suscripción'
            ], 500);
        }
    }

    /**
     * Obtener historial de suscripciones del usuario
     */
    public function getSubscriptionHistory()
    {
        try {
            $user = Auth::user();
            $subscriptions = $user->subscriptions()
                ->with('subscriptionPlan')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $subscriptions->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->subscriptionPlan->name,
                    'status' => $subscription->status,
                    'price_paid' => $subscription->price_paid,
                    'starts_at' => $subscription->starts_at->format('Y-m-d'),
                    'ends_at' => $subscription->ends_at->format('Y-m-d'),
                    'created_at' => $subscription->created_at->format('Y-m-d H:i:s'),
                    'cancelled_at' => $subscription->cancelled_at?->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting subscription history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial de suscripciones'
            ], 500);
        }
    }

    /**
     * Cancelar suscripción
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $user = Auth::user();
            $subscription = $user->activeSubscription;

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una suscripción activa para cancelar'
                ], 400);
            }

            DB::beginTransaction();

            // Cancelar la suscripción
            $subscription->cancel();

            Log::info('Subscription cancelled', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'plan' => $subscription->subscriptionPlan->name
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tu suscripción ha sido cancelada exitosamente. Podrás seguir disfrutando de los beneficios hasta el ' . $subscription->ends_at->format('d/m/Y')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la suscripción'
            ], 500);
        }
    }

    /**
     * Reactivar suscripción suspendida
     */
    public function reactivateSubscription(Request $request)
    {
        try {
            $user = Auth::user();
            $subscription = $user->subscriptions()
                ->where('status', 'suspended')
                ->latest()
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una suscripción suspendida para reactivar'
                ], 400);
            }

            DB::beginTransaction();

            // Reactivar la suscripción
            $subscription->reactivate();

            Log::info('Subscription reactivated', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'plan' => $subscription->subscriptionPlan->name
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tu suscripción ha sido reactivada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reactivating subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar la suscripción'
            ], 500);
        }
    }

    /**
     * Actualizar método de pago
     */
    public function updatePaymentMethod(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'payment_token' => 'required|string',
            'payment_method_type' => 'required|string|in:CARD,PSE,NEQUI',
            'last_four_digits' => 'nullable|string|max:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $subscription = $user->activeSubscription;

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una suscripción activa'
                ], 400);
            }

            DB::beginTransaction();

            // Actualizar método de pago
            $subscription->updatePaymentMethod(
                $request->payment_token,
                $request->payment_method_type,
                $request->last_four_digits
            );

            Log::info('Payment method updated', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'payment_method_type' => $request->payment_method_type
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Método de pago actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el método de pago'
            ], 500);
        }
    }

    /**
     * Obtener planes de suscripción disponibles
     */
    public function getAvailablePlans()
    {
        try {
            $plans = SubscriptionPlan::where('is_active', true)
                ->orderBy('price', 'asc')
                ->get();

            $data = $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'price' => $plan->price,
                    'description' => $plan->description,
                    'features' => $plan->features,
                    'is_popular' => $plan->is_popular,
                ];
            });

            return response()->json([
                'subscription_plans' => $data,
                'meta' => [
                    'total_plans' => $data->count(),
                    'currency' => 'COP'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting available plans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los planes disponibles'
            ], 500);
        }
    }
}