<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\QrPass;
use App\Models\SystemSetting;
use App\Models\TrekkingLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckAnomalies extends Command
{
    protected $signature   = 'anomaly:check {--force : Jalankan meski belum waktunya}';
    protected $description = 'Cek anomali pendaki: belum checkout setelah batas waktu, atau log terhenti.';

    public function handle(): int
    {
        $intervalMinutes = (int) SystemSetting::get('anomaly_check_interval_minutes', 30);
        $lastRun         = Cache::get('anomaly:last_run');

        // Lewati jika belum waktunya, kecuali flag --force digunakan
        if (!$this->option('force') && $lastRun) {
            $minutesSinceLast = Carbon::parse($lastRun)->diffInMinutes(now());
            if ($minutesSinceLast < $intervalMinutes) {
                $this->line("Belum waktunya. Interval: {$intervalMinutes} menit. Terakhir jalan: {$minutesSinceLast} menit lalu.");
                return self::SUCCESS;
            }
        }

        $this->info('[' . now()->toDateTimeString() . '] Menjalankan cek anomali...');

        $lateCheckouts  = $this->checkLateCheckouts();
        $stalledHikers  = $this->checkStalledLogs();

        Cache::put('anomaly:last_run', now()->toIso8601String(), now()->addHours(24));

        $this->info("Selesai. Late checkout: {$lateCheckouts} | Stalled: {$stalledHikers}");

        return self::SUCCESS;
    }

    /**
     * Alert 1: Pendaki yang melewati checkout_deadline_hour tapi belum scan gate_out.
     */
    private function checkLateCheckouts(): int
    {
        $graceMinutes = (int) SystemSetting::get('anomaly_checkout_grace_minutes', 0);
        $count        = 0;

        // Ambil semua QrPass aktif yang masa berlakunya sudah habis
        $overduePasses = QrPass::with([
                'participant.booking.mountain.regulation',
                'participant.booking.trail',
                'participant.user',
            ])
            ->where('status', 'active')
            ->where('valid_until', '<=', now()->subMinutes($graceMinutes))
            ->get();

        foreach ($overduePasses as $pass) {
            // Cek apakah sudah ada scan gate_out (direction: down) di checkpoint gate_out
            $hasCheckedOut = TrekkingLog::where('qr_pass_id', $pass->id)
                ->whereHas('checkpoint', fn ($q) => $q->where('type', 'gate_out'))
                ->where('direction', 'down')
                ->exists();

            if ($hasCheckedOut) {
                // Normalkan status jika sudah checkout tapi belum diupdate
                $pass->update(['status' => 'used']);
                continue;
            }

            $booking     = $pass->participant->booking;
            $mountain    = $booking->mountain;
            $participant = $pass->participant;

            $logContext = [
                'type'          => 'late_checkout',
                'booking_code'  => $booking->booking_code,
                'mountain'      => $mountain->name,
                'trail'         => $booking->trail->name,
                'participant'   => $participant->name,
                'valid_until'   => $pass->valid_until->toDateTimeString(),
                'overdue_since' => $pass->valid_until->diffForHumans(),
            ];

            Log::channel('stack')->warning('ANOMALI: Pendaki belum checkout', $logContext);

            $this->warn(
                "LATE CHECKOUT — {$participant->name} | {$mountain->name} | deadline: {$pass->valid_until->toDateTimeString()}"
            );

            // Tandai QrPass sebagai expired agar tidak dipakai lagi
            $pass->update(['status' => 'expired']);

            // TODO (fase produksi): kirim notifikasi WhatsApp/email ke pengelola TN
            // Notification::send($mountain->pengelola, new LateCheckoutAlert($pass, $logContext));

            $count++;
        }

        return $count;
    }

    /**
     * Alert 2 (opsional): Pendaki yang log scan-nya terhenti lebih dari X jam.
     */
    private function checkStalledLogs(): int
    {
        $thresholdHours = (int) SystemSetting::get('anomaly_stall_threshold_hours', 6);
        $count          = 0;

        // QrPass yang masih aktif dan belum expired
        $activePasses = QrPass::with(['participant.booking.mountain', 'participant'])
            ->where('status', 'active')
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get();

        foreach ($activePasses as $pass) {
            $lastLog = TrekkingLog::where('qr_pass_id', $pass->id)
                ->latest('scanned_at')
                ->first();

            // Jika belum ada log sama sekali atau log terakhir lebih dari threshold jam lalu
            $lastActivity = $lastLog
                ? Carbon::parse($lastLog->scanned_at)
                : Carbon::parse($pass->valid_from);

            $hoursSinceActivity = $lastActivity->diffInHours(now());

            if ($hoursSinceActivity < $thresholdHours) {
                continue;
            }

            $booking     = $pass->participant->booking;
            $participant = $pass->participant;

            $logContext = [
                'type'                 => 'stalled_log',
                'booking_code'         => $booking->booking_code,
                'mountain'             => $booking->mountain->name,
                'participant'          => $participant->name,
                'last_activity'        => $lastActivity->toDateTimeString(),
                'hours_since_activity' => $hoursSinceActivity,
            ];

            Log::channel('stack')->warning('ANOMALI: Log pendaki terhenti', $logContext);

            $this->warn(
                "STALLED — {$participant->name} | {$booking->mountain->name} | tidak ada aktivitas {$hoursSinceActivity} jam"
            );

            // TODO (fase produksi): kirim notifikasi ke pengelola TN
            // Notification::send($mountain->pengelola, new StalledHikerAlert($pass, $logContext));

            $count++;
        }

        return $count;
    }
}
