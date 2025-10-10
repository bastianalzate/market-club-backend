<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendBillingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:send-billing-reminders 
                            {--days=3 : Días antes del cobro para enviar recordatorio}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar recordatorios por email antes del próximo cobro de suscripción';

    protected $emailService;
    protected $sentCount = 0;

    /**
     * Create a new command instance.
     */
    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysBeforeBilling = (int) $this->option('days');
        $targetDate = now()->addDays($daysBeforeBilling)->startOfDay();

        $this->info("🔔 Enviando recordatorios para cobros en {$daysBeforeBilling} días...");

        // Obtener suscripciones que se cobrarán en X días
        $subscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->whereNotNull('payment_token')
            ->whereDate('next_billing_date', '=', $targetDate->toDateString())
            ->get();

        $this->info("📋 Suscripciones encontradas: {$subscriptions->count()}");

        if ($subscriptions->isEmpty()) {
            $this->info('✅ No hay recordatorios para enviar hoy');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($subscriptions->count());
        $progressBar->start();

        foreach ($subscriptions as $subscription) {
            try {
                $this->emailService->sendUpcomingBillingReminderEmail(
                    $subscription->user,
                    $subscription
                );

                $this->sentCount++;
                
            } catch (\Exception $e) {
                Log::error("Error enviando recordatorio de facturación", [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'error' => $e->getMessage(),
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('═══════════════════════════════════════');
        $this->info('📊 RESUMEN');
        $this->info('═══════════════════════════════════════');
        $this->info("📧 Recordatorios enviados: {$this->sentCount}");
        $this->info('═══════════════════════════════════════');

        return 0;
    }
}

