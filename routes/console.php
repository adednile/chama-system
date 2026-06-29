<?php

use App\Console\Commands\CalculateDailyPenalties;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // ✅ Add this import

// Define the command (you already have this)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Define the penalty command
Artisan::command('chama:penalties', function () {
    $this->call(CalculateDailyPenalties::class);
})->purpose('Apply daily penalties');

// Define the backup command
Artisan::command('chama:backup', function () {
    $this->call(\App\Console\Commands\BackupDatabase::class);
})->purpose('Encrypt and back up database state');

// ✅ Schedule the commands to run daily
Schedule::command('chama:penalties')->daily();
Schedule::command('chama:backup')->daily();