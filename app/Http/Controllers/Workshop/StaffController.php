<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopStaff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class StaffController extends Controller
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
            abort(403, 'Hanya admin bengkel yang dapat mengelola staf.');
        }

        return $workshop;
    }

    /**
     * Display a listing of the staff members.
     */
    public function index(Request $request): View
    {
        $workshop = $this->getOwnedWorkshop($request);

        $search = $request->input('search');
        $query = $workshop->staff()->with('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $staffMembers = $query->orderBy('joined_at', 'desc')->paginate(10)->withQueryString();

        return view('workshop.staff.index', compact('staffMembers', 'search'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create(Request $request): View
    {
        $this->getOwnedWorkshop($request);

        return view('workshop.staff.create');
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $workshop = $this->getOwnedWorkshop($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'regex:/^(\+?62|0)[0-9]{8,13}$/'],
            'position' => ['required', 'string', Rule::in([WorkshopStaff::POSITION_MECHANIC, WorkshopStaff::POSITION_ADMIN])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($workshop, $validated) {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_WORKSHOP,
            ]);

            // Create workshop staff record
            WorkshopStaff::create([
                'workshop_id' => $workshop->id,
                'user_id' => $user->id,
                'position' => $validated['position'],
                'is_active' => true,
                'joined_at' => now(),
            ]);
        });

        return redirect()
            ->route('workshop.staff.index')
            ->with('success', 'Staf berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(Request $request, WorkshopStaff $staff): View
    {
        $workshop = $this->getOwnedWorkshop($request);

        if ($staff->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        $staff->load('user');

        return view('workshop.staff.edit', compact('staff'));
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, WorkshopStaff $staff): RedirectResponse
    {
        $workshop = $this->getOwnedWorkshop($request);

        if ($staff->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($staff->user_id)],
            'phone_number' => ['required', 'string', 'regex:/^(\+?62|0)[0-9]{8,13}$/'],
            'position' => ['required', 'string', Rule::in([WorkshopStaff::POSITION_MECHANIC, WorkshopStaff::POSITION_ADMIN])],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($staff, $validated, $request) {
            $user = $staff->user;

            $userFields = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
            ];

            if (!empty($validated['password'])) {
                $userFields['password'] = Hash::make($validated['password']);
            }

            $user->update($userFields);

            $staff->update([
                'position' => $validated['position'],
                'is_active' => $request->has('is_active'),
            ]);
        });

        return redirect()
            ->route('workshop.staff.index')
            ->with('success', 'Staf berhasil diperbarui.');
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy(Request $request, WorkshopStaff $staff): RedirectResponse
    {
        $workshop = $this->getOwnedWorkshop($request);

        if ($staff->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        // Deleting the user will cascade delete the workshop staff record
        $staff->user->delete();

        return redirect()
            ->route('workshop.staff.index')
            ->with('success', 'Staf berhasil dihapus.');
    }
}
