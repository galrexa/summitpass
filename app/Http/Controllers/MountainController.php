<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use App\Models\MountainRegulation;
use App\Models\Trail;
use App\Models\TrailCheckpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MountainController extends Controller
{
    /**
     * Daftar semua gunung (publik)
     */
    public function index(Request $request)
    {
        $query = Mountain::with('regulation')->active();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        $mountains = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => $mountains->items(),
            'meta' => [
                'current_page' => $mountains->currentPage(),
                'last_page'    => $mountains->lastPage(),
                'per_page'     => $mountains->perPage(),
                'total'        => $mountains->total(),
            ],
        ], 200);
    }

    /**
     * Detail gunung beserta jalur dan regulasi
     */
    public function show($id)
    {
        $mountain = Mountain::with(['regulation', 'trails' => function ($q) {
            $q->active()->orderBy('route_order');
        }])->find($id);

        if (!$mountain) {
            return response()->json(['message' => 'Gunung tidak ditemukan'], 404);
        }

        return response()->json(['data' => $mountain], 200);
    }

    /**
     * Buat gunung baru beserta regulasinya (admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                        => 'required|string|max:255',
            'location'                    => 'required|string|max:255',
            'province'                    => 'nullable|string|max:100',
            'height_mdpl'                 => 'required|integer|min:0',
            'difficulty'                  => 'required|in:Easy,Moderate,Hard',
            'description'                 => 'nullable|string',
            'image_url'                   => 'nullable|url',
            // Regulasi (wajib saat buat gunung)
            'regulation.base_price'               => 'required|numeric|min:0',
            'regulation.quota_per_trail_per_day'  => 'required|integer|min:1',
            'regulation.max_hiking_days'          => 'required|integer|min:1',
            'regulation.max_participants_per_account' => 'required|integer|min:1',
            'regulation.guide_required'           => 'required|boolean',
            'regulation.checkout_deadline_hour'   => 'required|integer|between:0,23',
        ]);

        $mountain = DB::transaction(function () use ($validated) {
            $mountain = Mountain::create([
                'name'        => $validated['name'],
                'location'    => $validated['location'],
                'province'    => $validated['province'] ?? null,
                'height_mdpl' => $validated['height_mdpl'],
                'difficulty'  => $validated['difficulty'],
                'description' => $validated['description'] ?? null,
                'image_url'   => $validated['image_url'] ?? null,
            ]);

            MountainRegulation::create(array_merge(
                $validated['regulation'],
                ['mountain_id' => $mountain->id]
            ));

            return $mountain;
        });

        return response()->json([
            'message' => 'Gunung berhasil ditambahkan',
            'data'    => $mountain->load('regulation'),
        ], 201);
    }

    /**
     * Update data gunung (admin only)
     */
    public function update(Request $request, $id)
    {
        $mountain = Mountain::find($id);

        if (!$mountain) {
            return response()->json(['message' => 'Gunung tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'location'    => 'sometimes|string|max:255',
            'province'    => 'nullable|string|max:100',
            'height_mdpl' => 'sometimes|integer|min:0',
            'difficulty'  => 'sometimes|in:Easy,Moderate,Hard',
            'description' => 'nullable|string',
            'image_url'   => 'nullable|url',
            'is_active'   => 'sometimes|boolean',
            // Regulasi (opsional, update sebagian)
            'regulation.base_price'               => 'sometimes|numeric|min:0',
            'regulation.quota_per_trail_per_day'  => 'sometimes|integer|min:1',
            'regulation.max_hiking_days'          => 'sometimes|integer|min:1',
            'regulation.max_participants_per_account' => 'sometimes|integer|min:1',
            'regulation.guide_required'           => 'sometimes|boolean',
            'regulation.checkout_deadline_hour'   => 'sometimes|integer|between:0,23',
        ]);

        DB::transaction(function () use ($mountain, $validated) {
            $mountainData = array_diff_key($validated, ['regulation' => null]);
            if ($mountainData) {
                $mountain->update($mountainData);
            }

            if (!empty($validated['regulation'])) {
                $mountain->regulation()->updateOrCreate(
                    ['mountain_id' => $mountain->id],
                    $validated['regulation']
                );
            }
        });

        return response()->json([
            'message' => 'Gunung berhasil diperbarui',
            'data'    => $mountain->fresh()->load('regulation'),
        ], 200);
    }

    /**
     * Hapus gunung (admin only)
     */
    public function destroy($id)
    {
        $mountain = Mountain::find($id);

        if (!$mountain) {
            return response()->json(['message' => 'Gunung tidak ditemukan'], 404);
        }

        $mountain->delete();

        return response()->json(['message' => 'Gunung berhasil dihapus'], 200);
    }

    /**
     * Daftar jalur pendakian per gunung
     */
    public function getTrails($mountainId)
    {
        $mountain = Mountain::find($mountainId);

        if (!$mountain) {
            return response()->json(['message' => 'Gunung tidak ditemukan'], 404);
        }

        $trails = $mountain->trails()->active()->orderBy('route_order')->get();

        return response()->json(['data' => $trails], 200);
    }

    /**
     * Daftar pos/checkpoint per jalur
     */
    public function getCheckpoints($mountainId, $trailId)
    {
        $trail = Trail::where('mountain_id', $mountainId)->find($trailId);

        if (!$trail) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $checkpoints = $trail->checkpoints()->orderBy('order_seq')->get();

        return response()->json(['data' => $checkpoints], 200);
    }
}
