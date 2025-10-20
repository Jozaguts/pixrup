<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::call(static function () {
    User::where('usage_reset_at', '<', now())
        ->update([
            'usage_count' => 0,
            'usage_reset_at' => now()->addMonth(),
        ]);
})->dailyAt('00:10')->name('reset-monthly-usage');