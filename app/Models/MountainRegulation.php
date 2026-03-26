<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MountainRegulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'mountain_id',
        'base_price',
        'price_weekend',
        'price_foreign_weekday',
        'price_foreign_weekend',
        'price_student',
        'price_student_weekend',
        'minor_must_be_accompanied',
        'quota_per_trail_per_day',
        'quota_total_per_day',
        'max_hiking_days',
        'max_participants_per_account',
        'guide_required',
        'guide_price_per_day',
        'checkout_deadline_hour',
        'min_elevation_experience',
    ];

    protected $casts = [
        'base_price'               => 'decimal:2',
        'price_weekend'            => 'decimal:2',
        'price_foreign_weekday'    => 'decimal:2',
        'price_foreign_weekend'    => 'decimal:2',
        'price_student'            => 'decimal:2',
        'price_student_weekend'    => 'decimal:2',
        'guide_required'           => 'boolean',
        'guide_price_per_day'      => 'decimal:2',
        'minor_must_be_accompanied'=> 'boolean',
        'checkout_deadline_hour'   => 'integer',
        'min_elevation_experience' => 'integer',
    ];

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    /**
     * Hitung harga per orang.
     *
     * @param  bool  $isForeign  true jika wisatawan mancanegara (punya paspor, tidak punya NIK)
     * @param  bool  $isWeekend  true jika Sabtu atau Minggu
     * @param  bool  $isStudent  true jika peserta berumur < 17 tahun
     */
    public function priceFor(bool $isForeign, bool $isWeekend, bool $isStudent = false): float
    {
        // Mancanegara tidak mendapat harga pelajar
        if ($isForeign) {
            if ($isWeekend) {
                return (float) ($this->price_foreign_weekend ?? $this->price_foreign_weekday ?? $this->base_price);
            }
            return (float) ($this->price_foreign_weekday ?? $this->base_price);
        }

        if ($isStudent) {
            if ($isWeekend) {
                return (float) ($this->price_student_weekend ?? $this->price_student ?? $this->price_weekend ?? $this->base_price);
            }
            return (float) ($this->price_student ?? $this->base_price);
        }

        if ($isWeekend) {
            return (float) ($this->price_weekend ?? $this->base_price);
        }

        return (float) $this->base_price;
    }

    /**
     * Ekstrak umur dari NIK Indonesia.
     * Format NIK: PPKKKK DDMMYY SSSSSS (16 digit)
     * Digit 7-8 = tanggal lahir (wanita: +40), digit 9-10 = bulan, digit 11-12 = tahun (2 digit).
     *
     * @return int|null  Umur dalam tahun, atau null jika NIK tidak valid / bukan WNI
     */
    public static function ageFromNik(string $nik): ?int
    {
        if (strlen($nik) !== 16 || ! ctype_digit($nik)) {
            return null;
        }

        $dd  = (int) substr($nik, 6, 2);
        $mm  = (int) substr($nik, 8, 2);
        $yy  = (int) substr($nik, 10, 2);

        // Perempuan: dd > 40
        if ($dd > 40) {
            $dd -= 40;
        }

        // Tentukan abad: jika 2000+yy masih di masa depan, berarti 1900-an
        $currentYear = (int) now()->format('Y');
        $year = (2000 + $yy) > $currentYear ? (1900 + $yy) : (2000 + $yy);

        // Validasi dasar
        if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
            return null;
        }

        try {
            $birthDate = \Carbon\Carbon::createFromDate($year, $mm, $dd);
        } catch (\Exception $e) {
            return null;
        }

        return (int) $birthDate->age;
    }

    public function getFormattedBasePriceAttribute()
    {
        return 'Rp' . number_format($this->base_price, 0, ',', '.');
    }
}
