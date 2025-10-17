<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar comando de renovaciones automáticas de suscripciones
// TEMPORAL: Se ejecuta hoy a las 7:18 PM para procesar suscripción del usuario 27
Schedule::command('subscriptions:process-renewals')
    ->dailyAt('19:18')
    ->timezone('America/Bogota')
    ->withoutOverlapping()
    ->runInBackground();

// Programar envío de recordatorios de próximo cobro
// Se ejecuta diariamente a las 10:00 AM (3 días antes del cobro)
Schedule::command('subscriptions:send-billing-reminders --days=3')
    ->dailyAt('10:00')
    ->timezone('America/Bogota')
    ->withoutOverlapping();
