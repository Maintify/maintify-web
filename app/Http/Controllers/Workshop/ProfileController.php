<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Get the workshop owned by the authenticated user (admin).
     */
    private function getOwnedWorkshop(Request $request): Workshop
    {
        /** @var User $user */
        $user = $request->user();
        $workshop = $user->workshop;

        if (!$workshop) {
            abort(403, 'Hanya admin bengkel yang dapat mengelola profil bengkel.');
        }

        return $workshop;
    }

    /**
     * Show the form for editing the workshop profile.
     */
    public function edit(Request $request): View
    {
        $workshop = $this->getOwnedWorkshop($request);

        return view('workshop.profile.edit', compact('workshop'));
    }

    /**
     * Update the workshop profile in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $workshop = $this->getOwnedWorkshop($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:1000'],
            'operational_hours' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $workshop->update($validated);

        return redirect()
            ->route('workshop.profile.edit')
            ->with('success', 'Profil bengkel berhasil diperbarui.');
    }
}
