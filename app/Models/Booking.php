<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'leader_user_id',
        'mountain_id',
        'trail_id',          // jalur NAIK (gate IN)
        'trail_out_id',      // jalur TURUN (gate OUT) — null = sama dengan trail_id
        'is_cross_trail',
        'start_date',
        'end_date',
        'booking_code',
        'guide_requested',
        'tos_accepted_at',
        'status',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'tos_accepted_at'=> 'datetime',
        'is_cross_trail' => 'boolean',
        'guide_requested'=> 'boolean',
        'total_price'    => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    /** Jalur naik (gate IN) */
    public function trail()
    {
        return $this->belongsTo(Trail::class, 'trail_id');
    }

    /** Jalur turun (gate OUT) — null berarti sama dengan trail naik */
    public function trailOut()
    {
        return $this->belongsTo(Trail::class, 'trail_out_id');
    }

    public function participants()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    // ── Cross-Trail Helpers ────────────────────────────────────

    /**
     * Trail yang digunakan untuk gate OUT (efektif).
     * Jika bukan lintas jalur, kembalikan trail naik.
     */
    public function effectiveTrailOut(): Trail
    {
        if ($this->is_cross_trail && $this->trail_out_id) {
            return $this->trailOut ?? $this->trail;
        }
        return $this->trail;
    }

    /**
     * Kumpulan trail_id yang valid untuk di-scan oleh pendaki ini.
     * Dipakai validator scan untuk menolak checkpoint yang benar-benar
     * di luar rute (bukan sekadar jalur yang berbeda tapi masih valid).
     */
    public function validTrailIds(): array
    {
        $ids = [$this->trail_id];
        if ($this->is_cross_trail && $this->trail_out_id) {
            $ids[] = $this->trail_out_id;
        }
        return array_unique($ids);
    }

    /**
     * Apakah checkpoint ini valid untuk booking ini?
     * Memperhitungkan shared_checkpoint_group (mis. puncak bersama).
     */
    public function isCheckpointValid(TrailCheckpoint $checkpoint): bool
    {
        // Langsung cocok dengan salah satu trail yang terdaftar
        if (in_array($checkpoint->trail_id, $this->validTrailIds())) {
            return true;
        }

        // Cek via shared_checkpoint_group — pos yang sama secara fisik
        if ($checkpoint->shared_checkpoint_group) {
            $validTrailIds = $this->validTrailIds();
            return TrailCheckpoint::where('shared_checkpoint_group', $checkpoint->shared_checkpoint_group)
                ->whereIn('trail_id', $validTrailIds)
                ->exists();
        }

        return false;
    }

    /**
     * Apakah pendakian sudah selesai?
     * Logic: ada TrekkingLog dengan type=gate_out, direction=down,
     *        dan checkpoint milik effectiveTrailOut.
     */
    public function isCompleted(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }

        $gateOutTrailId = $this->effectiveTrailOut()->id;

        return TrekkingLog::whereHas('qrPass', fn($q) =>
                $q->whereHas('participant', fn($p) =>
                    $p->where('booking_id', $this->id)
                )
            )
            ->where('direction', 'down')
            ->whereHas('checkpoint', fn($cp) =>
                $cp->where('trail_id', $gateOutTrailId)
                   ->where('type', 'gate_out')
            )
            ->exists();
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeByLeader($query, $userId)
    {
        return $query->where('leader_user_id', $userId);
    }

    public function scopeCrossTrail($query)
    {
        return $query->where('is_cross_trail', true);
    }

    // ── Static Helpers ────────────────────────────────────────

    public static function generateBookingCode(): string
    {
        do {
            $code = 'SP-' . strtoupper(Str::random(8));
        } while (static::where('booking_code', $code)->exists());

        return $code;
    }
}
