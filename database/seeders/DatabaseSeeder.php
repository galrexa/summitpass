<?php

namespace Database\Seeders;

use App\Models\Mountain;
use App\Models\MountainRegulation;
use App\Models\Trail;
use App\Models\TrailCheckpoint;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'name'        => 'Gunung Rinjani',
            'location'    => 'Lombok Utara',
            'province'    => 'Nusa Tenggara Barat',
            'height_mdpl' => 3726,
            'difficulty'  => 'Hard',
            'description' => 'Gunung berapi aktif tertinggi kedua di Indonesia, terletak di Pulau Lombok.',
            'is_active'   => true,
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
            'name'        => 'Gunung Gede',
            'location'    => 'Cianjur / Sukabumi',
            'province'    => 'Jawa Barat',
            'height_mdpl' => 2958,
            'difficulty'  => 'Moderate',
            'description' => 'Gunung populer di Taman Nasional Gunung Gede Pangrango.',
            'is_active'   => true,
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
    }
}
