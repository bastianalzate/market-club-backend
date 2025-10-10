<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Models\PaymentTransaction;
use App\Services\WompiService;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:process-renewals 
                            {--dry-run : Ejecutar en modo prueba sin procesar pagos reales}
                            {--force : Forzar procesamiento incluso si no es el día de facturación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesar renovaciones automáticas de suscripciones con pagos recurrentes';

    protected $wompiService;
    protected $emailService;
    protected $processedCount = 0;
    protected $failedCount = 0;
    protected $skippedCount = 0;

    /**
     * Create a new command instance.
     */
    public function __construct(WompiService $wompiService, EmailService $emailService)
    {
        parent::__construct();
        $this->wompiService = $wompiService;
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');

        $this->info('🔄 Iniciando proceso de renovación de suscripciones...');
        
        if ($isDryRun) {
            $this->warn('⚠️  MODO PRUEBA: No se procesarán pagos reales');
        }

        // Obtener suscripciones pendientes de renovación
        $query = UserSubscription::with(['user', 'subscriptionPlan'])
            ->pendingRenewal();

        if (!$isForce) {
            // Solo procesar suscripciones cuya fecha de renovación es hoy o anterior
            $query->where('next_billing_date', '<=', now());
        }

        $subscriptions = $query->get();

        $this->info("📋 Suscripciones a procesar: {$subscriptions->count()}");

        if ($subscriptions->isEmpty()) {
            $this->info('✅ No hay suscripciones pendientes de renovación');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($subscriptions->count());
        $progressBar->start();

        foreach ($subscriptions as $subscription) {
            $this->processSubscriptionRenewal($subscription, $isDryRun);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Mostrar resumen
        $this->displaySummary();

        // Enviar notificación de resumen por email (solo si no es dry-run)
        if (!$isDryRun && ($this->processedCount > 0 || $this->failedCount > 0)) {
            $this->sendAdminSummaryEmail();
        }

        return 0;
    }

    /**
     * Procesar renovación de una suscripción
     */
    protected function processSubscriptionRenewal(UserSubscription $subscription, bool $isDryRun)
    {
        try {
            $user = $subscription->user;
            $plan = $subscription->subscriptionPlan;

            $this->newLine();
            $this->info("🔄 Procesando: Usuario #{$user->id} - {$user->email}");

            // Verificar si puede reintentar pago
            if (!$subscription->canRetryPayment()) {
                $this->warn("⏭️  Saltado: Máximo de reintentos alcanzado");
                $this->skippedCount++;
                return;
            }

            if ($isDryRun) {
                $this->info("   💳 [SIMULADO] Procesaría pago de \${$plan->price} COP");
                $this->processedCount++;
                return;
            }

            // Procesar pago real
            $this->processPayment($subscription, $user, $plan);

        } catch (\Exception $e) {
            $this->error("❌ Error procesando suscripción #{$subscription->id}: {$e->getMessage()}");
            Log::error("Error en renovación de suscripción #{$subscription->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->failedCount++;
        }
    }

    /**
     * Procesar pago de renovación
     */
    protected function processPayment(UserSubscription $subscription, $user, $plan)
    {
        DB::beginTransaction();

        try {
            // Generar referencia única
            $reference = $this->wompiService->generateRecurringReference(
                $subscription->id, 
                now()->format('Y-m')
            );

            // Procesar pago con Wompi
            $paymentResult = $this->wompiService->processRecurringPayment(
                $subscription->payment_token,
                (float) $plan->price,
                $reference,
                [
                    'email' => $user->email,
                    'name' => $user->name,
                    'phone' => $user->phone ?? '',
                ]
            );

            if (!$paymentResult['success']) {
                $errorMessage = is_array($paymentResult['error']) 
                    ? json_encode($paymentResult['error']) 
                    : ($paymentResult['error'] ?? 'Error desconocido en el pago');
                throw new \Exception($errorMessage);
            }

            // Verificar si el pago fue aprobado
            if ($this->wompiService->isPaymentApproved($paymentResult)) {
                $this->handleSuccessfulPayment($subscription, $paymentResult, $reference);
            } else {
                $errorMessage = $this->wompiService->getTransactionErrorMessage($paymentResult);
                throw new \Exception($errorMessage);
            }

            DB::commit();
            $this->processedCount++;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleFailedPayment($subscription, $e->getMessage());
            $this->failedCount++;
        }
    }

    /**
     * Manejar pago exitoso
     */
    protected function handleSuccessfulPayment(UserSubscription $subscription, array $paymentResult, string $reference)
    {
        $this->info("   ✅ Pago procesado exitosamente");

        // Registrar transacción
        $transaction = PaymentTransaction::create([
            'user_id' => $subscription->user_id,
            'order_id' => null,
            'transaction_id' => $paymentResult['data']['data']['id'] ?? null,
            'reference' => $reference,
            'amount' => $subscription->subscriptionPlan->price,
            'currency' => 'COP',
            'status' => 'APPROVED',
            'payment_method' => $subscription->payment_method_type,
            'metadata' => [
                'subscription_id' => $subscription->id,
                'renewal_type' => 'automatic',
                'billing_period' => now()->format('Y-m'),
            ],
        ]);

        // Renovar suscripción
        $subscription->renew(1);
        $subscription->recordSuccessfulPayment();

        // Enviar email de confirmación
        try {
            $this->emailService->sendSubscriptionRenewalSuccessEmail(
                $subscription->user,
                $subscription,
                $transaction
            );
        } catch (\Exception $e) {
            Log::error("Error enviando email de renovación exitosa", ['error' => $e->getMessage()]);
        }

        $this->info("   📧 Email de confirmación enviado");
    }

    /**
     * Manejar pago fallido
     */
    protected function handleFailedPayment(UserSubscription $subscription, string $errorMessage)
    {
        $this->error("   ❌ Pago fallido: {$errorMessage}");

        // Registrar intento fallido
        $subscription->recordFailedPayment($errorMessage);

        // Si fue el último intento, enviar email de suspensión
        if ($subscription->fresh()->isSuspended()) {
            try {
                $this->emailService->sendSubscriptionSuspendedEmail(
                    $subscription->user,
                    $subscription
                );
                $this->warn("   🔒 Suscripción suspendida - Email enviado");
            } catch (\Exception $e) {
                Log::error("Error enviando email de suspensión", ['error' => $e->getMessage()]);
            }
        } else {
            // Enviar email de fallo de pago
            try {
                $this->emailService->sendPaymentFailedEmail(
                    $subscription->user,
                    $subscription,
                    $errorMessage
                );
                $this->info("   📧 Email de fallo enviado");
            } catch (\Exception $e) {
                Log::error("Error enviando email de fallo de pago", ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Mostrar resumen de ejecución
     */
    protected function displaySummary()
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info('📊 RESUMEN DE PROCESAMIENTO');
        $this->info('═══════════════════════════════════════');
        $this->info("✅ Procesadas exitosamente: {$this->processedCount}");
        $this->error("❌ Fallidas: {$this->failedCount}");
        $this->warn("⏭️  Saltadas: {$this->skippedCount}");
        $this->info('═══════════════════════════════════════');
    }

    /**
     * Enviar email de resumen al administrador
     */
    protected function sendAdminSummaryEmail()
    {
        try {
            $adminEmail = config('mail.admin_email', config('mail.from.address'));
            
            $this->emailService->sendAdminRenewalSummaryEmail(
                $adminEmail,
                [
                    'processed' => $this->processedCount,
                    'failed' => $this->failedCount,
                    'skipped' => $this->skippedCount,
                    'date' => now()->format('Y-m-d H:i:s'),
                ]
            );

            $this->info("📧 Resumen enviado al administrador");
        } catch (\Exception $e) {
            Log::error("Error enviando resumen al administrador", ['error' => $e->getMessage()]);
        }
    }
}

