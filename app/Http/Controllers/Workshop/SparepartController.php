<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SparepartController extends Controller
{
    /**
     * Get the active workshop for the authenticated user.
     */
    private function getWorkshop(Request $request): ?Workshop
    {
        /** @var User $user */
        $user = $request->user();
        return $user->workshop ?? $user->workshopStaff?->workshop;
    }

    /**
     * Display a listing of the spareparts.
     */
    public function index(Request $request): View
    {
        $workshop = $this->getWorkshop($request);
        if (!$workshop) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');
        $query = $workshop->spareparts();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $spareparts = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('workshop.spareparts.index', compact('spareparts', 'search'));
    }

    /**
     * Show the form for creating a new sparepart.
     */
    public function create(Request $request): View
    {
        $workshop = $this->getWorkshop($request);
        if (!$workshop) {
            abort(403, 'Unauthorized.');
        }

        return view('workshop.spareparts.create');
    }

    /**
     * Store a newly created sparepart in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $workshop = $this->getWorkshop($request);
        if (!$workshop) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $workshop->spareparts()->create([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'price' => $validated['price'],
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        return redirect()
            ->route('workshop.spareparts.index')
            ->with('success', 'Sparepart berhasil ditambahkan ke katalog.');
    }

    /**
     * Show the form for editing the specified sparepart.
     */
    public function edit(Request $request, Sparepart $sparepart): View
    {
        $workshop = $this->getWorkshop($request);
        if (!$workshop || $sparepart->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        return view('workshop.spareparts.edit', compact('sparepart'));
    }

    /**
     * Update the specified sparepart in storage.
     */
    public function update(Request $request, Sparepart $sparepart): RedirectResponse
    {
        $workshop = $this->getWorkshop($request);
        if (!$workshop || $sparepart->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $sparepart->update([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'price' => $validated['price'],
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        return redirect()
            ->route('workshop.spareparts.index')
            ->with('success', 'Sparepart berhasil diperbarui.');
    }

    /**
     * Remove the specified sparepart from storage.
     */
    public function destroy(Request $request, Sparepart $sparepart): RedirectResponse
    {
        $workshop = $this->getWorkshop($request);
        if (!$workshop || $sparepart->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        $sparepart->delete();

        return redirect()
            ->route('workshop.spareparts.index')
            ->with('success', 'Sparepart berhasil dihapus dari katalog.');
    }
}
