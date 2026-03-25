<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\MountainRegulation;
use App\Models\Trail;
use App\Models\TrailCheckpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MountainWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Mountain::withCount('trails')->with('regulation');

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
        return view('admin.mountains.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'location'                        => 'required|string|max:255',
            'province'                        => 'nullable|string|max:100',
            'height_mdpl'                     => 'required|integer|min:0',
            'difficulty'                      => 'required|in:Easy,Moderate,Hard',
            'description'                     => 'nullable|string',
            'image_url'                       => 'nullable|url',
            'base_price'                      => 'required|numeric|min:0',
            'quota_per_trail_per_day'         => 'required|integer|min:1',
            'max_hiking_days'                 => 'required|integer|min:1',
            'max_participants_per_account'    => 'required|integer|min:1',
            'guide_required'                  => 'nullable|boolean',
            'checkout_deadline_hour'          => 'required|integer|between:0,23',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $mountain = Mountain::create([
                'name'        => $validated['name'],
                'location'    => $validated['location'],
                'province'    => $validated['province'] ?? null,
                'height_mdpl' => $validated['height_mdpl'],
                'difficulty'  => $validated['difficulty'],
                'description' => $validated['description'] ?? null,
                'image_url'   => $validated['image_url'] ?? null,
                'is_active'   => true,
            ]);

            MountainRegulation::create([
                'mountain_id'                  => $mountain->id,
                'base_price'                   => $validated['base_price'],
                'quota_per_trail_per_day'      => $validated['quota_per_trail_per_day'],
                'max_hiking_days'              => $validated['max_hiking_days'],
                'max_participants_per_account' => $validated['max_participants_per_account'],
                'guide_required'               => $request->boolean('guide_required'),
                'checkout_deadline_hour'       => $validated['checkout_deadline_hour'],
            ]);
        });

        return redirect()->route('admin.mountains.index')
            ->with('success', "Gunung {$validated['name']} berhasil ditambahkan.");
    }

    public function show($id)
    {
        $mountain = Mountain::with(['regulation', 'trails.checkpoints'])->findOrFail($id);

        return view('admin.mountains.show', compact('mountain'));
    }

    public function edit($id)
    {
        $mountain = Mountain::with('regulation')->findOrFail($id);

        return view('admin.mountains.edit', compact('mountain'));
    }

    public function update(Request $request, $id)
    {
        $mountain = Mountain::with('regulation')->findOrFail($id);

        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'location'                        => 'required|string|max:255',
            'province'                        => 'nullable|string|max:100',
            'height_mdpl'                     => 'required|integer|min:0',
            'difficulty'                      => 'required|in:Easy,Moderate,Hard',
            'description'                     => 'nullable|string',
            'image_url'                       => 'nullable|url',
            'is_active'                       => 'nullable|boolean',
            'base_price'                      => 'required|numeric|min:0',
            'quota_per_trail_per_day'         => 'required|integer|min:1',
            'max_hiking_days'                 => 'required|integer|min:1',
            'max_participants_per_account'    => 'required|integer|min:1',
            'guide_required'                  => 'nullable|boolean',
            'checkout_deadline_hour'          => 'required|integer|between:0,23',
        ]);

        DB::transaction(function () use ($mountain, $validated, $request) {
            $mountain->update([
                'name'        => $validated['name'],
                'location'    => $validated['location'],
                'province'    => $validated['province'] ?? null,
                'height_mdpl' => $validated['height_mdpl'],
                'difficulty'  => $validated['difficulty'],
                'description' => $validated['description'] ?? null,
                'image_url'   => $validated['image_url'] ?? null,
                'is_active'   => $request->boolean('is_active'),
            ]);

            $mountain->regulation()->updateOrCreate(
                ['mountain_id' => $mountain->id],
                [
                    'base_price'                   => $validated['base_price'],
                    'quota_per_trail_per_day'      => $validated['quota_per_trail_per_day'],
                    'max_hiking_days'              => $validated['max_hiking_days'],
                    'max_participants_per_account' => $validated['max_participants_per_account'],
                    'guide_required'               => $request->boolean('guide_required'),
                    'checkout_deadline_hour'       => $validated['checkout_deadline_hour'],
                ]
            );
        });

        return redirect()->route('admin.mountains.show', $mountain->id)
            ->with('success', 'Data gunung berhasil diperbarui.');
    }

    public function destroy($id)
    {
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

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'route_order' => 'required|integer|min:1',
        ]);

        $trail = $mountain->trails()->create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', "Jalur {$trail->name} berhasil ditambahkan.");
    }

    public function updateTrail(Request $request, $mountainId, $trailId)
    {
        $trail = Trail::where('mountain_id', $mountainId)->findOrFail($trailId);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'route_order' => 'required|integer|min:1',
            'is_active'   => 'nullable|boolean',
        ]);

        $trail->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'route_order' => $validated['route_order'],
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', 'Jalur berhasil diperbarui.');
    }

    public function destroyTrail($mountainId, $trailId)
    {
        $trail = Trail::where('mountain_id', $mountainId)->findOrFail($trailId);
        $trail->delete();

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', 'Jalur berhasil dihapus.');
    }

    // ── Checkpoint ─────────────────────────────────────────────────────────

    public function storeCheckpoint(Request $request, $mountainId, $trailId)
    {
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

    public function destroyCheckpoint($mountainId, $trailId, $checkpointId)
    {
        $checkpoint = TrailCheckpoint::where('trail_id', $trailId)
            ->where('mountain_id', $mountainId)
            ->findOrFail($checkpointId);

        $checkpoint->delete();

        return redirect()->route('admin.mountains.show', $mountainId)
            ->with('success', 'Pos berhasil dihapus.');
    }
}
