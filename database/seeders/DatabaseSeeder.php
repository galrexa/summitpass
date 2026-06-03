<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Mountain;
use App\Models\MountainRegulation;
use App\Models\QrPass;
use App\Models\Trail;
use App\Models\TrailCheckpoint;
use App\Models\TrekkingLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────
        User::factory()->admin()->create([
            'name'  => 'Admin SummitPass',
            'email' => 'admin@summitpass.id',
            'password' => Hash::make('admin123456'),
        ]);

        // ── Pengelola TN ──────────────────────────────────────────────────
        User::factory()->pengelolaTn()->create([
            'name'  => 'Pengelola Rinjani',
            'email' => 'pengelola@tngr.id',
            'password' => Hash::make('pengelola123'),
        ]);

        // ── Petugas Pos ───────────────────────────────────────────────────
        User::factory()->officer()->create([
            'name'  => 'Officer Pos 1',
            'email' => 'officer@summitpass.id',
            'password' => Hash::make('officer123'),
        ]);

        // ── Pendaki demo ──────────────────────────────────────────────────
        User::factory()->create([
            'name'     => 'Budi Pendaki',
            'email'    => 'budi@example.com',
            'nik'      => '3201010101010001',
            'password' => Hash::make('pendaki123'),
        ]);

        // ── Gunung Rinjani (contoh data) ──────────────────────────────────
        $rinjani = Mountain::create([
            'name'           => 'Gunung Rinjani',
            'location'       => 'Lombok Utara',
            'province'       => 'Nusa Tenggara Barat',
            'height_mdpl'    => 3726,
            'grade'          => 'IV',
            'description'    => 'Gunung berapi aktif tertinggi kedua di Indonesia, terletak di Pulau Lombok. Dengan kawah Segara Anak yang memukau dan pemandangan 360° dari puncaknya, Rinjani adalah pengalaman pendakian yang tak terlupakan.',
            'ecosystem_type' => 'Hutan hujan tropis, savana alpine, danau kawah vulkanik',
            'trail_status'   => 'open',
            'is_active'      => true,
            'latitude'       => -8.4119,
            'longitude'      => 116.4648,
        ]);

        MountainRegulation::create([
            'mountain_id'                  => $rinjani->id,
            'base_price'                   => 150000,
            'quota_per_trail_per_day'      => 150,
            'max_hiking_days'              => 4,
            'max_participants_per_account' => 10,
            'guide_required'               => true,
            'checkout_deadline_hour'       => 17,
        ]);

        $jalurSembalun = Trail::create([
            'mountain_id' => $rinjani->id,
            'name'        => 'Jalur Sembalun',
            'description' => 'Jalur populer via Sembalun Lawang, melewati padang savana.',
            'route_order' => 1,
            'is_active'   => true,
        ]);

        $checkpoints = [
            ['name' => 'Gerbang Sembalun',  'type' => 'gate_in',  'order_seq' => 1, 'altitude' => 1156, 'lat' => -8.3745, 'lng' => 116.4812],
            ['name' => 'Pos 1 Sembalun',    'type' => 'pos',      'order_seq' => 2, 'altitude' => 1500, 'lat' => -8.3601, 'lng' => 116.4723],
            ['name' => 'Pos 2 Sembalun',    'type' => 'pos',      'order_seq' => 3, 'altitude' => 1800, 'lat' => -8.3489, 'lng' => 116.4634],
            ['name' => 'Pos 3 Sembalun',    'type' => 'pos',      'order_seq' => 4, 'altitude' => 2200, 'lat' => -8.3312, 'lng' => 116.4545],
            ['name' => 'Pelawangan Sembalun','type' => 'pos',     'order_seq' => 5, 'altitude' => 2641, 'lat' => -8.3156, 'lng' => 116.4478],
            ['name' => 'Puncak Rinjani',    'type' => 'summit',   'order_seq' => 6, 'altitude' => 3726, 'lat' => -8.4119, 'lng' => 116.4667],
            ['name' => 'Gerbang Senaru',    'type' => 'gate_out', 'order_seq' => 7, 'altitude' => 601,  'lat' => -8.3001, 'lng' => 116.4223],
        ];

        foreach ($checkpoints as $cp) {
            TrailCheckpoint::create([
                'trail_id'    => $jalurSembalun->id,
                'mountain_id' => $rinjani->id,
                'name'        => $cp['name'],
                'type'        => $cp['type'],
                'order_seq'   => $cp['order_seq'],
                'altitude'    => $cp['altitude'],
                'latitude'    => $cp['lat'],
                'longitude'   => $cp['lng'],
            ]);
        }

        // ── Gunung Gede ───────────────────────────────────────────────────
        $gede = Mountain::create([
            'name'           => 'Gunung Gede',
            'location'       => 'Cianjur / Sukabumi',
            'province'       => 'Jawa Barat',
            'height_mdpl'    => 2958,
            'grade'          => 'III',
            'description'    => 'Gunung populer di Taman Nasional Gunung Gede Pangrango, cocok untuk pendaki menengah. Jalur via Cibodas melewati air terjun Cibeureum yang terkenal dan hutan montana yang rimbun.',
            'ecosystem_type' => 'Hutan montana, hutan hujan dataran tinggi, padang edelweiss',
            'trail_status'   => 'open',
            'is_active'      => true,
            'latitude'       => -6.7814,
            'longitude'      => 106.9834,
        ]);

        MountainRegulation::create([
            'mountain_id'                  => $gede->id,
            'base_price'                   => 29000,
            'quota_per_trail_per_day'      => 300,
            'max_hiking_days'              => 2,
            'max_participants_per_account' => 10,
            'guide_required'               => false,
            'checkout_deadline_hour'       => 17,
        ]);

        Trail::create([
            'mountain_id' => $gede->id,
            'name'        => 'Jalur Cibodas',
            'description' => 'Jalur utama via Cibodas, melewati air terjun Cibeureum.',
            'route_order' => 1,
            'is_active'   => true,
        ]);

        // ── Gunung Semeru (Grade IV - Advanced) ───────────────────────────
        $semeru = Mountain::create([
            'name'           => 'Gunung Semeru',
            'location'       => 'Lumajang',
            'province'       => 'Jawa Timur',
            'height_mdpl'    => 3676,
            'grade'          => 'IV',
            'description'    => 'Gunung tertinggi di Pulau Jawa. Dikenal dengan sebutan Mahameru, puncak tertinggi yang menantang dengan jalur berpasir dan cuaca ekstrem. Semeru adalah gunung berapi aktif dengan letusan periodik setiap 15-30 menit.',
            'ecosystem_type' => 'Hutan montana, padang savana, kawah aktif',
            'trail_status'   => 'open',
            'is_active'      => true,
            'latitude'       => -8.1077,
            'longitude'      => 112.9225,
        ]);

        MountainRegulation::create([
            'mountain_id'                  => $semeru->id,
            'base_price'                   => 50000,
            'quota_per_trail_per_day'      => 100,
            'max_hiking_days'              => 4,
            'max_participants_per_account' => 10,
            'guide_required'               => true,
            'guide_requirement_level'      => 'mandatory',
            'guide_ratio_max_hikers'       => 10,
            'guide_price_per_day'          => 150000,
            'checkout_deadline_hour'       => 14,
            'min_elevation_experience'     => 2500, // ← KUNCI: Butuh pengalaman 2500 MDPL
        ]);

        Trail::create([
            'mountain_id' => $semeru->id,
            'name'        => 'Jalur Ranu Pani',
            'description' => 'Jalur utama via Ranu Pani, melewati Ranu Kumbolo yang indah.',
            'grade'       => 'IV',
            'route_order' => 1,
            'is_active'   => true,
        ]);

        // ── Update Regulasi Gunung Existing ───────────────────────────────
        // Rinjani: Grade IV, butuh pengalaman 3000 MDPL
        MountainRegulation::where('mountain_id', $rinjani->id)->update([
            'min_elevation_experience' => 3000,
            'guide_requirement_level'  => 'mandatory',
        ]);

        // Gede: Grade III, butuh pengalaman 1500 MDPL
        MountainRegulation::where('mountain_id', $gede->id)->update([
            'min_elevation_experience' => 1500,
        ]);

        // ── Demo Users untuk Testing ──────────────────────────────────────
        // User 1: Pendaki Pemula (belum pernah mendaki)
        $pemula = User::factory()->create([
            'name'     => 'Andi Pemula',
            'email'    => 'pemula@example.com',
            'nik'      => '3201010101990001',
            'password' => Hash::make('pemula123'),
        ]);

        // User 2: Pendaki Berpengalaman (sudah pernah ke Gede)
        $expert = User::factory()->create([
            'name'     => 'Siti Berpengalaman',
            'email'    => 'expert@example.com',
            'nik'      => '3201010101980002',
            'password' => Hash::make('expert123'),
        ]);

        // Buat booking completed untuk expert ke Gede (untuk pengalaman 2958 MDPL)
        $trailGede = Trail::where('mountain_id', $gede->id)->first();
        $bookingGede = Booking::create([
            'leader_user_id' => $expert->id,
            'mountain_id'    => $gede->id,
            'trail_id'       => $trailGede->id,
            'booking_code'   => 'SP-DEMO-GEDE',
            'start_date'     => now()->subDays(30),
            'end_date'       => now()->subDays(28),
            'status'         => 'completed', // ← PENTING: Status completed
            'total_price'    => 29000,
            'tos_accepted_at'=> now()->subDays(30),
        ]);

        BookingParticipant::create([
            'booking_id' => $bookingGede->id,
            'user_id'    => $expert->id,
            'name'       => 'Siti Berpengalaman',
            'nik'        => '3201010101980002',
            'role'       => 'leader',
        ]);

        // ── Demo booking dengan guide & porter ────────────────────────────
        $pendaki = User::where('email', 'budi@example.com')->first();
        $pengelola = User::where('email', 'pengelola@tngr.id')->first();
        $rinjani->update(['pengelola_id' => $pengelola->id]);

        $booking = Booking::create([
            'leader_user_id' => $pendaki->id,
            'mountain_id'    => $rinjani->id,
            'trail_id'       => $jalurSembalun->id,
            'booking_code'   => 'SP-2026-001',
            'start_date'     => now()->addDays(3)->toDateString(),
            'end_date'       => now()->addDays(6)->toDateString(),
            'status'         => 'active',
            'total_price'    => 150000 * 3,
        ]);

        $participants = [
            ['name' => 'Budi Pendaki', 'nik' => '3201010101010001', 'role' => 'hiker', 'user_id' => $pendaki->id],
            ['name' => 'Pak Rudi Santoso', 'nik' => '3201234567890002', 'role' => 'guide', 'certification_number' => 'APGI-2024-00456'],
            ['name' => 'Asep Supriatna', 'nik' => '3201234567890003', 'role' => 'porter'],
        ];

        foreach ($participants as $pData) {
            $bp = BookingParticipant::create([
                'booking_id'           => $booking->id,
                'user_id'              => $pData['user_id'] ?? null,
                'name'                 => $pData['name'],
                'nik'                  => $pData['nik'],
                'role'                 => $pData['role'],
                'certification_number' => $pData['certification_number'] ?? null,
            ]);

            $qrPass = QrPass::create([
                'booking_participant_id' => $bp->id,
                'qr_token'               => QrPass::generateToken(),
                'valid_from'             => now()->addDays(3),
                'valid_until'            => now()->addDays(7),
                'status'                 => 'active',
                'family_token'           => Str::random(32),
            ]);
        }

        // Satu token hardcode untuk demo
        QrPass::first()?->update(['family_token' => 'demo-keluarga-001']);

        // Seed GPS coordinates untuk trekking logs yang ada
        TrekkingLog::all()->each(function ($log) {
            $mountain = $log->checkpoint?->trail?->mountain;
            if ($mountain?->latitude && !$log->latitude) {
                $log->update([
                    'latitude'  => $mountain->latitude  + (rand(-200, 200) / 10000),
                    'longitude' => $mountain->longitude + (rand(-200, 200) / 10000),
                ]);
            }
        });
    }
}
