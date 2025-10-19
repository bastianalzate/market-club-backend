<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Services\WompiService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessSubscriptionRenewals extends Command
{
    protected $signature = 'subscriptions:process-renewals';
    protected $description = 'Procesa las renovaciones automáticas de suscripciones';

    protected $wompiService;
    protected $emailService;

    public function __construct(WompiService $wompiService, EmailService $emailService)
    {
        parent::__construct();
        $this->wompiService = $wompiService;
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $this->info('🔄 Iniciando procesamiento de renovaciones de suscripciones...');

        // Obtener suscripciones que necesitan renovación
        $subscriptions = UserSubscription::pendingRenewal()
            ->with(['user', 'subscriptionPlan'])
            ->get();

        $this->info("📊 Encontradas {$subscriptions->count()} suscripciones pendientes de renovación");

        $successCount = 0;
        $failureCount = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $this->info("🔄 Procesando renovación para usuario {$subscription->user->email}...");
                
                $result = $this->processRenewal($subscription);
                
                if ($result['success']) {
                    $successCount++;
                    $this->info("✅ Renovación exitosa para {$subscription->user->email}");
                } else {
                    $failureCount++;
                    $this->error("❌ Fallo en renovación para {$subscription->user->email}: {$result['error']}");
                }
                
            } catch (\Exception $e) {
                $failureCount++;
                $this->error("❌ Error procesando renovación para {$subscription->user->email}: " . $e->getMessage());
                Log::error("Subscription renewal error", [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("📈 Resumen: {$successCount} exitosas, {$failureCount} fallidas");
        
        return Command::SUCCESS;
    }

    private function processRenewal(UserSubscription $subscription)
    {
        try {
            DB::beginTransaction();

            // Verificar si puede intentar el pago
            if (!$subscription->canRetryPayment()) {
                return [
                    'success' => false,
                    'error' => 'Máximo de intentos alcanzado o no es momento de reintentar'
                ];
            }

            // Crear transacción de renovación en Wompi
            $paymentData = [
                'amount_in_cents' => $this->wompiService->convertToCents($subscription->subscriptionPlan->price),
                'currency' => 'COP',
                'customer_email' => $subscription->user->email,
                'customer_name' => $subscription->user->name,
                'customer_phone' => $subscription->user->phone ?? '',
                'payment_method_type' => 'CARD',
                'payment_token' => $subscription->payment_token,
                'reference' => $this->wompiService->generateReference('RENEWAL_' . $subscription->id . '_' . time()),
                'installments' => 1,
            ];

            $result = $this->wompiService->createTransaction($paymentData);

            if ($result['success']) {
                $transaction = $result['data']['data'];
                
                // Registrar pago exitoso
                $subscription->recordSuccessfulPayment();
                
                // Renovar la suscripción
                $subscription->renew();
                
                // Crear registro de transacción
                \App\Models\PaymentTransaction::create([
                    'user_id' => $subscription->user_id,
                    'subscription_id' => $subscription->id,
                    'transaction_id' => $transaction['id'],
                    'reference' => $transaction['reference'],
                    'amount' => $subscription->subscriptionPlan->price,
                    'status' => 'approved',
                    'payment_method' => 'wompi',
                    'metadata' => [
                        'type' => 'subscription_renewal',
                        'subscription_plan' => $subscription->subscriptionPlan->name,
                        'renewed_at' => now(),
                    ],
                ]);

                // Enviar email de confirmación de renovación
                try {
                    $this->emailService->sendSubscriptionRenewalConfirmation($subscription);
                } catch (\Exception $e) {
                    Log::error("Failed to send renewal confirmation email: " . $e->getMessage());
                }

                DB::commit();

                return [
                    'success' => true,
                    'transaction_id' => $transaction['id']
                ];

            } else {
                // Registrar fallo de pago
                $subscription->recordFailedPayment($result['message'] ?? 'Payment failed');
                
                DB::commit();

                return [
                    'success' => false,
                    'error' => $result['message'] ?? 'Payment failed'
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}