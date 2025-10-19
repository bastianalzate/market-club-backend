<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class NotifySubscriptionExpirations extends Command
{
    protected $signature = 'subscriptions:notify-expirations {--days=7 : Días antes del vencimiento para notificar}';
    protected $description = 'Envía notificaciones de vencimiento de suscripciones';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $days = $this->option('days');
        $this->info("📧 Enviando notificaciones de vencimiento ({$days} días antes)...");

        // Obtener suscripciones que expiran pronto
        $subscriptions = UserSubscription::expiringSoon($days)
            ->with(['user', 'subscriptionPlan'])
            ->get();

        $this->info("📊 Encontradas {$subscriptions->count()} suscripciones próximas a vencer");

        $sentCount = 0;
        $errorCount = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $this->info("📧 Enviando notificación a {$subscription->user->email}...");
                
                $this->emailService->sendSubscriptionExpirationWarning($subscription, $days);
                $sentCount++;
                
                $this->info("✅ Notificación enviada a {$subscription->user->email}");
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ Error enviando notificación a {$subscription->user->email}: " . $e->getMessage());
                Log::error("Subscription expiration notification error", [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("📈 Resumen: {$sentCount} enviadas, {$errorCount} errores");
        
        return Command::SUCCESS;
    }
}
