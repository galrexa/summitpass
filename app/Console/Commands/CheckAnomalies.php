<?php

namespace App\Console\Commands;

use App\Models\QrPass;
use App\Models\TrekkingLog;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAnomalies extends Command
{
    protected $signature   = 'summitpass:check-anomalies';
    protected $description = 'Cek anomali pendaki: stalled log, overdue, dan checkpoint di luar rute lintas jalur';

    public function handle(): int
    {
        $this->info('Memulai pengecekan anomali...');

        $stalled  = $this->checkStalledLogs();
        $overdue  = $this->checkOverdueAtCheckpoint();
        $wrongOut = $this->checkWrongGateOut();

        $this->info("Selesai. Stalled: {$stalled} | Overdue: {$overdue} | Wrong gate-out: {$wrongOut}");

        return Command::SUCCESS;
    }

    /**
     * Pendaki yang sudah tidak ada aktivitas scan > threshold jam.
     * Tidak berubah, tapi sekarang juga menyertakan info cross-trail di log.
     */
    private function checkStalledLogs(): int
    {
        $thresholdHours = (int) SystemSetting::get('anomaly_stall_threshold_hours', 6);
        $count          = 0;

        $activePasses = QrPass::with([
                'participant.booking.mountain',
                'participant.booking.trail',
                'participant.booking.trailOut', // ← BARU
                'participant',
            ])
            ->where('status', 'active')
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get();

        foreach ($activePasses as $pass) {
            $lastLog = TrekkingLog::where('qr_pass_id', $pass->id)
                ->latest('scanned_at')
                ->first();

            $lastActivity = $lastLog
                ? Carbon::parse($lastLog->scanned_at)
                : Carbon::parse($pass->valid_from);

            $hoursSinceActivity = $lastActivity->diffInHours(now());

            if ($hoursSinceActivity < $thresholdHours) {
                continue;
            }

            $booking     = $pass->participant->booking;
            $participant = $pass->participant;

            // ← BARU: sertakan info rute lengkap termasuk cross-trail
            $routeInfo = $booking->is_cross_trail
                ? "{$booking->trail->name} (naik) → {$booking->effectiveTrailOut()->name} (turun)"
                : $booking->trail->name;

            $logContext = [
                'type'                 => 'stalled_log',
                'booking_code'         => $booking->booking_code,
                'mountain'             => $booking->mountain->name,
                'route'                => $routeInfo,
                'is_cross_trail'       => $booking->is_cross_trail,
                'participant'          => $participant->name,
                'last_activity'        => $lastActivity->toDateTimeString(),
                'hours_since_activity' => $hoursSinceActivity,
            ];

            Log::channel('stack')->warning('ANOMALI: Log pendaki terhenti', $logContext);
            $this->warn("STALLED — {$participant->name} | {$booking->mountain->name} | {$routeInfo} | idle {$hoursSinceActivity} jam");

            $count++;
        }

        return $count;
    }

    /**
     * BARU: Pendaki lintas jalur yang sudah melewati estimasi waktu
     * untuk tiba di pos berikutnya berdasarkan estimated_duration_minutes.
     */
    private function checkOverdueAtCheckpoint(): int
    {
        $bufferMultiplier = 1.5; // 50% buffer di atas estimasi
        $count = 0;

        $activePasses = QrPass::with([
                'participant.booking.trail.checkpoints',
                'participant.booking.trailOut.checkpoints',
                'participant',
            ])
            ->where('status', 'active')
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get();

        foreach ($activePasses as $pass) {
            $lastLog = TrekkingLog::where('qr_pass_id', $pass->id)
                ->with('checkpoint')
                ->latest('scanned_at')
                ->first();

            if (!$lastLog) {
                continue;
            }

            $lastCheckpoint = $lastLog->checkpoint;
            if (!$lastCheckpoint || !$lastCheckpoint->estimated_duration_minutes) {
                continue;
            }

            $estimatedArrival = Carbon::parse($lastLog->scanned_at)
                ->addMinutes((int) ($lastCheckpoint->estimated_duration_minutes * $bufferMultiplier));

            if (now()->lessThan($estimatedArrival)) {
                continue;
            }

            $booking     = $pass->participant->booking;
            $participant = $pass->participant;
            $overdueMin  = (int) $estimatedArrival->diffInMinutes(now());

            $logContext = [
                'type'            => 'overdue_checkpoint',
                'booking_code'    => $booking->booking_code,
                'mountain'        => $booking->mountain->name,
                'is_cross_trail'  => $booking->is_cross_trail,
                'participant'     => $participant->name,
                'last_checkpoint' => $lastCheckpoint->name,
                'direction'       => $lastLog->direction,
                'overdue_minutes' => $overdueMin,
            ];

            Log::channel('stack')->warning('ANOMALI: Pendaki overdue di pos', $logContext);
            $this->warn("OVERDUE — {$participant->name} | pos terakhir: {$lastCheckpoint->name} | terlambat {$overdueMin} menit");

            // Flag anomali pada log terakhir
            $lastLog->flagAnomaly("Overdue {$overdueMin} menit dari estimasi tiba pos berikutnya.");

            $count++;
        }

        return $count;
    }

    /**
     * BARU: Pendaki lintas jalur yang melakukan scan turun
     * di gate_out yang berbeda dari trail_out_id yang dideklarasikan
     * (tanpa anomaly_flag sebelumnya = belum terdeteksi).
     */
    private function checkWrongGateOut(): int
    {
        $count = 0;

        // Hanya booking lintas jalur yang aktif
        $activePasses = QrPass::with([
                'participant.booking.trail',
                'participant.booking.trailOut',
                'participant',
            ])
            ->whereHas('participant.booking', fn($q) => $q->where('is_cross_trail', true)->where('status', 'active'))
            ->where('status', 'active')
            ->get();

        foreach ($activePasses as $pass) {
            $booking = $pass->participant->booking;

            // Cari log scan gate_out arah turun
            $gateOutLog = TrekkingLog::where('qr_pass_id', $pass->id)
                ->where('direction', 'down')
                ->whereHas('checkpoint', fn($q) => $q->where('type', 'gate_out'))
                ->with('checkpoint')
                ->latest('scanned_at')
                ->first();

            if (!$gateOutLog) {
                continue;
            }

            $declaredOutTrailId = $booking->effectiveTrailOut()->id;

            // Jika gate_out yang di-scan bukan dari jalur turun yang dideklarasikan
            if ($gateOutLog->checkpoint->trail_id !== $declaredOutTrailId && !$gateOutLog->anomaly_flag) {
                $reason = "Checkout di gate_out '{$gateOutLog->checkpoint->name}' "
                    . "(trail_id={$gateOutLog->checkpoint->trail_id}) "
                    . "berbeda dari jalur turun yang dideklarasikan: "
                    . "'{$booking->effectiveTrailOut()->name}' (trail_id={$declaredOutTrailId}).";

                $gateOutLog->flagAnomaly($reason);

                Log::channel('stack')->warning('ANOMALI: Checkout di gate berbeda dari deklarasi', [
                    'booking_code'   => $booking->booking_code,
                    'participant'    => $pass->participant->name,
                    'declared_out'   => $booking->effectiveTrailOut()->name,
                    'actual_out'     => $gateOutLog->checkpoint->name,
                ]);

                $this->warn("WRONG GATE-OUT — {$pass->participant->name} | deklarasi: {$booking->effectiveTrailOut()->name} | aktual: {$gateOutLog->checkpoint->name}");
                $count++;
            }
        }

        return $count;
    }
}
