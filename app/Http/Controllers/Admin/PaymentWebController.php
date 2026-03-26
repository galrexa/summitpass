<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.mountain', 'booking.trail', 'booking.leader'])
            ->latest('created_at');

        $user = auth()->user();
        $pengelolaMountainId = null;
        if ($user->role === 'pengelola_tn') {
            $pengelolaMountainId = $user->managedMountain?->id;
            if ($pengelolaMountainId) {
                $query->whereHas('booking', fn($q) => $q->where('mountain_id', $pengelolaMountainId));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('mountain_id') && !$pengelolaMountainId) {
            $query->whereHas('booking', fn ($q) => $q->where('mountain_id', $request->mountain_id));
        }

        if ($request->filled('search')) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_code', 'like', "%{$request->search}%")
                  ->orWhereHas('leader', fn ($u) =>
                      $u->where('name', 'like', "%{$request->search}%")
                  );
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(20)->withQueryString();

        $mountainsQuery = Mountain::orderBy('name');
        if ($pengelolaMountainId) {
            $mountainsQuery->where('id', $pengelolaMountainId);
        }
        $mountains = $mountainsQuery->get(['id', 'name']);

        $summaryQuery = Payment::query();
        if ($pengelolaMountainId) {
            $summaryQuery->whereHas('booking', fn($q) => $q->where('mountain_id', $pengelolaMountainId));
        } elseif ($user->role === 'pengelola_tn') {
            $summaryQuery->whereRaw('1 = 0');
        }

        $summary = [
            'total_paid'    => (clone $summaryQuery)->where('status', 'paid')->sum('amount'),
            'total_pending' => (clone $summaryQuery)->where('status', 'pending')->sum('amount'),
            'count_paid'    => (clone $summaryQuery)->where('status', 'paid')->count(),
            'count_pending' => (clone $summaryQuery)->where('status', 'pending')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'mountains', 'summary'));
    }
}
