<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\MountainRegulation;
use App\Models\Trail;
use App\Models\TrailCheckpoint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MountainWebController extends Controller
{
    private function authorizeForMountain(Mountain $mountain): void
    {
        $user = auth()->user();
        if ($user->role === 'pengelola_tn' && $mountain->pengelola_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke gunung ini.');
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Mountain::withCount('trails')->with(['regulation', 'pengelola']);

        // Pengelola_tn hanya melihat gunung miliknya
        if ($user->role === 'pengelola_tn') {
            $query->where('pengelola_id', $user->id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $mountains = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.mountains.index', compact('mountains'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menambah gunung baru.');
        }

        $pengelolaList = User::whereHas('userRole', fn($q) => $q->where('name', 'pengelola_tn'))
            ->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.mountains.create', compact('pengelolaList'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menambah gunung baru.');
        }

        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'location'                        => 'required|string|max:255',
            'province'                        => 'nullable|string|max:100',
            'height_mdpl'                     => 'required|integer|min:0',
            'difficulty'                      => 'required|in:Easy,Moderate,Hard',
            'description'                     => 'nullable|string',
            'image_url'                       => 'nullable|url',
            'pengelola_id'                    => 'nullable|exists:users,id',
            'base_price'                      => 'required|numeric|min:0',
            'price_weekend'                   => 'nullable|numeric|min:0',
            'price_foreign_weekday'           => 'nullable|numeric|min:0',
            'price_foreign_weekend'           => 'nullable|numeric|min:0',
            'price_student'                   => 'nullable|numeric|min:0',
            'price_student_weekend'           => 'nullable|numeric|min:0',
            'minor_must_be_accompanied'       => 'nullable|boolean',
            'quota_per_trail_per_day'         => 'required|integer|min:1',
            'quota_total_per_day'             => 'nullable|integer|min:1',
            'max_hiking_days'                 => 'required|integer|min:1',
            'max_participants_per_account'    => 'required|integer|min:1',
            'guide_required'                  => 'nullable|boolean',
            'guide_price_per_day'             => 'nullable|numeric|min:0',
            'checkout_deadline_hour'          => 'required|integer|between:0,23',
            'min_elevation_experience'        => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $mountain = Mountain::create([
                'name'         => $validated['name'],
                'location'     => $validated['location'],
                'province'     => $validated['province'] ?? null,
                'height_mdpl'  => $validated['height_mdpl'],
                'difficulty'   => $validated['difficulty'],
                'description'  => $validated['description'] ?? null,
                'image_url'    => $validated['image_url'] ?? null,
                'is_active'    => true,
                'pengelola_id' => $validated['pengelola_id'] ?? null,
            ]);

            MountainRegulation::create([
                'mountain_id'                  => $mountain->id,
                'base_price'                   => $validated['base_price'],
                'price_weekend'                => $validated['price_weekend'] ?? null,
                'price_foreign_weekday'        => $validated['price_foreign_weekday'] ?? null,
                'price_foreign_weekend'        => $validated['price_foreign_weekend'] ?? null,
                'price_student'                => $validated['price_student'] ?? null,
                'price_student_weekend'        => $validated['price_student_weekend'] ?? null,
                'minor_must_be_accompanied'    => $request->boolean('minor_must_be_accompanied', true),
                'quota_per_trail_per_day'      => $validated['quota_per_trail_per_day'],
                'quota_total_per_day'          => $validated['quota_total_per_day'] ?? null,
                'max_hiking_days'              => $validated['max_hiking_days'],
                'max_participants_per_account' => $validated['max_participants_per_account'],
                'guide_required'               => $request->boolean('guide_required'),
                'guide_price_per_day'          => $validated['guide_price_per_day'] ?? null,
                'checkout_deadline_hour'       => $validated['checkout_deadline_hour'],
                'min_elevation_experience'     => $validated['min_elevation_experience'] ?? null,
            ]);
        });

        return redirect()->route('admin.mountains.index')
            ->with('success', "Gunung {$validated['name']} berhasil ditambahkan.");
    }

    public function show($id)
    {
        $mountain = Mountain::with(['regulation', 'trails.checkpoints', 'pengelola'])->findOrFail($id);
        $this->authorizeForMountain($mountain);

        return view('admin.mountains.show', compact('mountain'));
    }

    public function edit($id)
    {
        $mountain = Mountain::with(['regulation', 'pengelola'])->findOrFail($id);
        $this->authorizeForMountain($mountain);

        $pengelolaList = collect();
        if (auth()->user()->role === 'admin') {
            $pengelolaList = User::whereHas('userRole', fn($q) => $q->where('name', 'pengelola_tn'))
                ->orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('admin.mountains.edit', compact('mountain', 'pengelolaList'));
    }

    public function update(Request $request, $id)
    {
        $mountain = Mountain::with('regulation')->findOrFail($id);
        $this->authorizeForMountain($mountain);

        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'location'                        => 'required|string|max:255',
            'province'                        => 'nullable|string|max:100',
            'height_mdpl'                     => 'required|integer|min:0',
            'difficulty'                      => 'required|in:Easy,Moderate,Hard',
            'description'                     => 'nullable|string',
            'image_url'                       => 'nullable|url',
            'is_active'                       => 'nullable|boolean',
            'pengelola_id'                    => 'nullable|exists:users,id',
            'base_price'                      => 'required|numeric|min:0',
            'price_weekend'                   => 'nullable|numeric|min:0',
            'price_foreign_weekday'           => 'nullable|numeric|min:0',
            'price_foreign_weekend'           => 'nullable|numeric|min:0',
            'price_student'                   => 'nullable|numeric|min:0',
            'price_student_weekend'           => 'nullable|numeric|min:0',
            'minor_must_be_accompanied'       => 'nullable|boolean',
            'quota_per_trail_per_day'         => 'required|integer|min:1',
            'quota_total_per_day'             => 'nullable|integer|min:1',
            'max_hiking_days'                 => 'required|integer|min:1',
            'max_participants_per_account'    => 'required|integer|min:1',
            'guide_required'                  => 'nullable|boolean',
            'guide_price_per_day'             => 'nullable|numeric|min:0',
            'checkout_deadline_hour'          => 'required|integer|between:0,23',
            'min_elevation_experience'        => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($mountain, $validated, $request) {
            $updateData = [
                'name'        => $validated['name'],
                'location'    => $validated['location'],
                'province'    => $validated['province'] ?? null,
                'height_mdpl' => $validated['height_mdpl'],
                'difficulty'  => $validated['difficulty'],
                'description' => $validated['description'] ?? null,
                'image_url'   => $validated['image_url'] ?? null,
                'is_active'   => $request->boolean('is_active'),
            ];

            // Hanya admin yang bisa mengubah pengelola
            if (auth()->user()->role === 'admin') {
                $updateData['pengelola_id'] = $validated['pengelola_id'] ?? null;
            }

            $mountain->update($updateData);

            $mountain->regulation()->updateOrCreate(
                ['mountain_id' => $mountain->id],
                [
                    'base_price'                   => $validated['base_price'],
                    'price_weekend'                => $validated['price_weekend'] ?? null,
                    'price_foreign_weekday'        => $validated['price_foreign_weekday'] ?? null,
                    'price_foreign_weekend'        => $validated['price_foreign_weekend'] ?? null,
                    'price_student'                => $validated['price_student'] ?? null,
                    'price_student_weekend'        => $validated['price_student_weekend'] ?? null,
                    'minor_must_be_accompanied'    => $request->boolean('minor_must_be_accompanied', true),
                    'quota_per_trail_per_day'      => $validated['quota_per_trail_per_day'],
                    'quota_total_per_day'          => $validated['quota_total_per_day'] ?? null,
                    'max_hiking_days'              => $validated['max_hiking_days'],
                    'max_participants_per_account' => $validated['max_participants_per_account'],
                    'guide_required'               => $request->boolean('guide_required'),
                    'guide_price_per_day'          => $validated['guide_price_per_day'] ?? null,
                    'checkout_deadline_hour'       => $validated['checkout_deadline_hour'],
                    'min_elevation_experience'     => $validated['min_elevation_experience'] ?? null,
                ]
            );
        });

        return redirect()->route('admin.mountains.show', $mountain->id)
            ->with('success', 'Data gunung berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus gunung.');
        }

        $mountain = Mountain::findOrFail($id);
        $name = $mountain->name;
        $mountain->delete();

        return redirect()->route('admin.mountains.index')
            ->with('success', "Gunung {$name} berhasil dihapus.");
    }

    // ── Trail ──────────────────────────────────────────────────────────────

    public function storeTrail(Request $request, $mountainId)
    {
        $mountain = Mountain::findOrFail($mountainId);
        $this->authorizeForMountain($mountain);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'route_order'   => 'required|integer|min:1',
            'quota_per_day' => 'nullable|integer|min:1',
        ]);

        $trail = $mountain->trails()->create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', "Jalur {$trail->name} berhasil ditambahkan.");
    }

    public function updateTrail(Request $request, $mountainId, $trailId)
    {
        $mountain = Mountain::findOrFail($mountainId);
        $this->authorizeForMountain($mountain);

        $trail = Trail::where('mountain_id', $mountainId)->findOrFail($trailId);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'route_order'   => 'required|integer|min:1',
            'is_active'     => 'nullable|boolean',
            'quota_per_day' => 'nullable|integer|min:1',
        ]);

        $trail->update([
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'route_order'   => $validated['route_order'],
            'is_active'     => $request->boolean('is_active'),
            'quota_per_day' => $validated['quota_per_day'] ?? null,
        ]);

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', 'Jalur berhasil diperbarui.');
    }

    public function destroyTrail($mountainId, $trailId)
    {
        $mountain = Mountain::findOrFail($mountainId);
        $this->authorizeForMountain($mountain);

        $trail = Trail::where('mountain_id', $mountainId)->findOrFail($trailId);
        $trail->delete();

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', 'Jalur berhasil dihapus.');
    }

    // ── Checkpoint ─────────────────────────────────────────────────────────

    public function storeCheckpoint(Request $request, $mountainId, $trailId)
    {
        $mountain = Mountain::findOrFail($mountainId);
        $this->authorizeForMountain($mountain);

        $trail = Trail::where('mountain_id', $mountainId)->findOrFail($trailId);

        $validated = $request->validate([
            'name'                       => 'required|string|max:255',
            'description'                => 'nullable|string',
            'order_seq'                  => 'required|integer|min:1',
            'type'                       => 'required|in:gate_in,pos,summit,gate_out',
            'latitude'                   => 'required|numeric|between:-90,90',
            'longitude'                  => 'required|numeric|between:-180,180',
            'altitude'                   => 'nullable|integer',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
        ]);

        $trail->checkpoints()->create(array_merge($validated, ['mountain_id' => $mountainId]));

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', "Pos {$validated['name']} berhasil ditambahkan.");
    }

    public function updateCheckpoint(Request $request, $mountainId, $trailId, $checkpointId)
    {
        $mountain = Mountain::findOrFail($mountainId);
        $this->authorizeForMountain($mountain);

        $checkpoint = TrailCheckpoint::where('trail_id', $trailId)
            ->where('mountain_id', $mountainId)
            ->findOrFail($checkpointId);

        $validated = $request->validate([
            'name'                       => 'required|string|max:255',
            'description'                => 'nullable|string',
            'order_seq'                  => 'required|integer|min:1',
            'type'                       => 'required|in:gate_in,pos,summit,gate_out',
            'latitude'                   => 'required|numeric|between:-90,90',
            'longitude'                  => 'required|numeric|between:-180,180',
            'altitude'                   => 'nullable|integer',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
        ]);

        $checkpoint->update($validated);

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', "Pos {$validated['name']} berhasil diperbarui.");
    }

    public function destroyCheckpoint($mountainId, $trailId, $checkpointId)
    {
        $mountain = Mountain::findOrFail($mountainId);
        $this->authorizeForMountain($mountain);

        $checkpoint = TrailCheckpoint::where('trail_id', $trailId)
            ->where('mountain_id', $mountainId)
            ->findOrFail($checkpointId);

        $checkpoint->delete();

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', 'Pos berhasil dihapus.');
    }
}
