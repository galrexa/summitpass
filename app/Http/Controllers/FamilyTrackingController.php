<?php

namespace App\Http\Controllers;

use App\Models\QrPass;

class FamilyTrackingController extends Controller
{
    public function show($token)
    {
        $qrPass = QrPass::with([
            'participant.booking.mountain',
            'participant.booking.trail.checkpoints',
            'trekkingLogs.checkpoint',
        ])->where('family_token', $token)->first();

        if (!$qrPass) {
            return view('public.family-tracking-notfound');
        }

        return view('public.family-tracking', compact('qrPass'));
    }
}
