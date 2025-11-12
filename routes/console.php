<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\UsagePropertyMonthly;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
$timezone = config('usage.timezone', 'UTC');
$resetTime = config('usage.reset_cron_time', '00:10');
$retentionMonths = (int) config('usage.retention_months', 12);

Schedule::call(static function () use ($timezone): void {
    $now = now($timezone);
    $nextReset = $now->copy()->startOfMonth()->addMonth()->startOfDay();

    $affected = User::query()
        ->whereNull('usage_reset_at')
        ->orWhere('usage_reset_at', '<=', $now->toDateTimeString())
        ->update([
            'usage_count' => 0,
            'usage_reset_at' => $nextReset->toDateTimeString(),
        ]);

    Log::channel('usage')->info('usage.reset.completed', [
        'affected' => $affected,
        'ran_at' => $now->toIso8601String(),
    ]);
})->timezone($timezone)
    ->monthlyOn(1, $resetTime)
    ->name('reset-monthly-usage');

Schedule::call(static function () use ($retentionMonths): void {
    $threshold = now('UTC')->subMonths($retentionMonths);

    $deleted = UsagePropertyMonthly::query()
        ->where('first_action_at', '<', $threshold)
        ->delete();

    Log::channel('usage')->info('usage.cleanup.completed', [
        'deleted' => $deleted,
        'threshold' => $threshold->toIso8601String(),
    ]);
})->timezone('UTC')
    ->weeklyOn(0, '02:00')
    ->name('usage-historical-cleanup');
