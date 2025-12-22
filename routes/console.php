<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule SOP expiry check to run daily at 8:00 AM
Schedule::command('sop:check-expiry --days=30')->dailyAt('08:00');

// Schedule SOP anniversary check (1-year review & 3-year update) to run daily at 8:05 AM
Schedule::command('sop:check-anniversary')->dailyAt('08:05');
