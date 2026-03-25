<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands — SummitPass
|--------------------------------------------------------------------------
|
| Scheduler dijalankan setiap menit oleh cron OS:
|   * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
|
| Interval cek anomali TIDAK di-hardcode di sini.
| Command anomaly:check membaca interval dari tabel system_settings
| dan menggunakan cache untuk menentukan kapan harus berjalan.
|
*/

// Jalankan setiap menit — command itu sendiri yang menentukan
// apakah sudah waktunya berdasarkan setting anomaly_check_interval_minutes.
Schedule::command('anomaly:check')->everyMinute()->withoutOverlapping();
