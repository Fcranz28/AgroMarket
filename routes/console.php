<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Mantenimiento de las colas (requiere el scheduler: `php artisan schedule:run` cada minuto).
 * Los jobs fallidos se conservan una semana para poder revisarlos y reintentarlos
 * (`php artisan queue:failed` / `queue:retry`).
 */
Schedule::command('queue:prune-failed', ['--hours' => 168])->daily();
Schedule::command('queue:prune-batches')->daily();
