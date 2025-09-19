<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Obtener todos los planes de suscripción disponibles
     */
    public function getPlans()
    {
        try {
            $plans = SubscriptionPlan::active()
                ->ordered()
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->slug,
                        'name' => $plan->name,
                        'price' => (string) $plan->price,
                        'currency' => 'COP',
                        'period' => 'mes',
                        'description' => $plan->description,
                        'features' => $plan->features,
                        'is_popular' => $plan->slug === 'collector_brewer', // Marcar el plan del medio como popular
                        'is_active' => (bool) $plan->is_active,
                        'created_at' => $plan->created_at->toISOString(),
                        'updated_at' => $plan->updated_at->toISOString(),
                    ];
                });

            return response()->json([
                'subscription_plans' => $plans,
                'meta' => [
                    'total_plans' => $plans->count(),
                    'currency' => 'COP',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los planes de suscripción',
            ], 500);
        }
    }

    /**
     * Suscribir al usuario a un plan
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|string|exists:subscription_plans,slug',
            'payment_method' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $plan = SubscriptionPlan::where('slug', $request->plan_id)->firstOrFail();
            $durationMonths = $request->duration_months ?? 1;

            // Verificar si el usuario ya tiene una suscripción activa
            $activeSubscription = $user->activeSubscription;
            if ($activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya tienes una suscripción activa. Cancela tu suscripción actual antes de suscribirte a un nuevo plan.',
                    'current_subscription' => [
                        'plan_name' => $activeSubscription->subscriptionPlan->name,
                        'ends_at' => $activeSubscription->ends_at->format('Y-m-d'),
                        'days_remaining' => $activeSubscription->days_remaining,
                    ],
                ], 400);
            }

            // Crear nueva suscripción
            $startsAt = now();
            $endsAt = $startsAt->copy()->addMonths($durationMonths);

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'price_paid' => $plan->price * $durationMonths,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'next_billing_date' => $endsAt,
                'metadata' => [
                    'payment_method' => $request->payment_method,
                    'duration_months' => $durationMonths,
                    'subscribed_at' => now(),
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Suscripción creada exitosamente',
                'data' => [
                    'subscription' => [
                        'id' => $subscription->id,
                        'plan_name' => $plan->name,
                        'price_paid' => (float) $subscription->price_paid,
                        'starts_at' => $subscription->starts_at->format('Y-m-d'),
                        'ends_at' => $subscription->ends_at->format('Y-m-d'),
                        'days_remaining' => $subscription->days_remaining,
                        'status' => $subscription->status,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la suscripción',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener la suscripción actual del usuario
     */
    public function getCurrentSubscription()
    {
        try {
            $user = Auth::user();
            $activeSubscription = $user->activeSubscription;

            if (!$activeSubscription) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No tienes una suscripción activa',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activeSubscription->id,
                    'plan' => [
                        'id' => $activeSubscription->subscriptionPlan->slug,
                        'name' => $activeSubscription->subscriptionPlan->name,
                        'price' => (string) $activeSubscription->subscriptionPlan->price,
                        'currency' => 'COP',
                        'period' => 'mes',
                        'description' => $activeSubscription->subscriptionPlan->description,
                        'features' => $activeSubscription->subscriptionPlan->features,
                        'is_popular' => $activeSubscription->subscriptionPlan->slug === 'collector_brewer',
                        'is_active' => (bool) $activeSubscription->subscriptionPlan->is_active,
                    ],
                    'price_paid' => (float) $activeSubscription->price_paid,
                    'status' => $activeSubscription->status,
                    'starts_at' => $activeSubscription->starts_at->format('Y-m-d'),
                    'ends_at' => $activeSubscription->ends_at->format('Y-m-d'),
                    'days_remaining' => $activeSubscription->days_remaining,
                    'is_active' => $activeSubscription->isActive(),
                    'next_billing_date' => $activeSubscription->next_billing_date?->format('Y-m-d'),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la suscripción',
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
                ->paginate(10);

            $formattedSubscriptions = $subscriptions->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->subscriptionPlan->name,
                    'price_paid' => (float) $subscription->price_paid,
                    'status' => $subscription->status,
                    'starts_at' => $subscription->starts_at->format('Y-m-d'),
                    'ends_at' => $subscription->ends_at->format('Y-m-d'),
                    'cancelled_at' => $subscription->cancelled_at?->format('Y-m-d H:i:s'),
                    'created_at' => $subscription->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'subscriptions' => $formattedSubscriptions,
                    'pagination' => [
                        'current_page' => $subscriptions->currentPage(),
                        'per_page' => $subscriptions->perPage(),
                        'total' => $subscriptions->total(),
                        'last_page' => $subscriptions->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial de suscripciones',
            ], 500);
        }
    }

    /**
     * Cancelar suscripción actual
     */
    public function cancelSubscription()
    {
        try {
            $user = Auth::user();
            $activeSubscription = $user->activeSubscription;

            if (!$activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una suscripción activa para cancelar',
                ], 400);
            }

            $activeSubscription->cancel();

            return response()->json([
                'success' => true,
                'message' => 'Suscripción cancelada exitosamente',
                'data' => [
                    'cancelled_at' => $activeSubscription->cancelled_at->format('Y-m-d H:i:s'),
                    'plan_name' => $activeSubscription->subscriptionPlan->name,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la suscripción',
            ], 500);
        }
    }

    /**
     * Renovar suscripción
     */
    public function renewSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'duration_months' => 'nullable|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $activeSubscription = $user->activeSubscription;

            if (!$activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una suscripción activa para renovar',
                ], 400);
            }

            $durationMonths = $request->duration_months ?? 1;
            $activeSubscription->renew($durationMonths);

            return response()->json([
                'success' => true,
                'message' => 'Suscripción renovada exitosamente',
                'data' => [
                    'new_end_date' => $activeSubscription->fresh()->ends_at->format('Y-m-d'),
                    'days_remaining' => $activeSubscription->fresh()->days_remaining,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al renovar la suscripción',
            ], 500);
        }
    }
}
