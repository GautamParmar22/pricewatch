<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// GP - 19-08-2026 code comment - Register the 5-minute pricing check scheduler
use App\Jobs\CheckDueCompetitors;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CheckDueCompetitors)->everyFiveMinutes();
