<?php

namespace App\Http\Controllers\Pendaki;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\TrekkingLog;
use Illuminate\Support\Facades\Auth;

class PendakiController extends Controller
{
    public function bookings()
    {
        return view('pendaki.bookings');
    }

    public function trekkingLog()
    {
        $user = Auth::user();

        // All QrPass IDs belonging to this user (as participant)
        $participantQrPassIds = BookingParticipant::where('user_id', $user->id)
            ->whereHas('qrPass')
            ->with('qrPass:id,booking_participant_id')
            ->get()
            ->pluck('qrPass.id')
            ->filter();

        $logs = TrekkingLog::with(['checkpoint', 'qrPass.participant.booking.mountain'])
            ->whereIn('qr_pass_id', $participantQrPassIds)
            ->orderByDesc('scanned_at')
            ->get();

        // Group logs by booking
        $groupedLogs = $logs->groupBy(fn($log) => $log->qrPass?->participant?->booking_id)
            ->map(fn($bookingLogs) => [
                'booking'  => $bookingLogs->first()->qrPass?->participant?->booking,
                'mountain' => $bookingLogs->first()->qrPass?->participant?->booking?->mountain,
                'logs'     => $bookingLogs->sortByDesc('scanned_at'),
                'first_scan' => $bookingLogs->min('scanned_at'),
                'last_scan'  => $bookingLogs->max('scanned_at'),
            ])
            ->sortByDesc(fn($g) => $g['first_scan'])
            ->values();

        $completedBookings = Booking::where('leader_user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $uniqueMountainsCount = Booking::where('leader_user_id', $user->id)
            ->where('status', 'completed')
            ->distinct('mountain_id')
            ->count('mountain_id');

        $totalScans = $logs->count();

        return view('pendaki.trekking-log', compact('completedBookings', 'uniqueMountainsCount', 'logs', 'groupedLogs', 'totalScans'));
    }

    public function myPass()
    {
        $user = Auth::user();

        $bookings = Booking::with([
                'mountain', 'trail',
                'participants' => fn($q) => $q->where('user_id', $user->id)->with('qrPass'),
            ])
            ->where('leader_user_id', $user->id)
            ->whereIn('status', ['paid', 'active'])
            ->orderBy('start_date')
            ->get();

        return view('pendaki.my-pass', compact('user', 'bookings'));
    }

    public function jejakSummit()
    {
        $user = Auth::user();

        $completedBookings = Booking::with('mountain')
            ->where('leader_user_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('end_date')
            ->get();

        $summitPoints    = $completedBookings->sum(fn($b) => $b->mountain?->height_mdpl ?? 0);
        $uniqueMountains = $completedBookings->unique('mountain_id');
        $highestMdpl     = $completedBookings->max(fn($b) => $b->mountain?->height_mdpl ?? 0);

        // Badge definitions: [id, label, desc, condition(bool), color]
        $allBadges = [
            ['first_step',   'Langkah Pertama', 'Selesaikan pendakian pertamamu',        $uniqueMountains->count() >= 1,  '#16a34a'],
            ['explorer',     'Penjelajah',       'Daki 3 gunung berbeda',                 $uniqueMountains->count() >= 3,  '#2563eb'],
            ['peak_hunter',  'Pemburu Puncak',   'Daki 5 gunung berbeda',                 $uniqueMountains->count() >= 5,  '#7c3aed'],
            ['high_altitude','Pendaki Andal',    'Capai ketinggian ≥ 3.000 mdpl',         $highestMdpl >= 3000,            '#d97706'],
            ['summit_lord',  'Lord of Summit',   'Capai ketinggian ≥ 3.726 mdpl (Rinjani)',$highestMdpl >= 3726,          '#dc2626'],
            ['point_master', 'Point Master',     'Kumpulkan 10.000 Summit Points',         $summitPoints >= 10000,         '#0891b2'],
        ];

        return view('pendaki.jejak-summit', compact(
            'user', 'completedBookings', 'summitPoints',
            'uniqueMountains', 'highestMdpl', 'allBadges'
        ));
    }

    public function profile()
    {
        return view('pendaki.profile', ['user' => Auth::user()]);
    }

    public function settings()
    {
        return view('pendaki.settings', ['user' => Auth::user()]);
    }
}
