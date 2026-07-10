<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request, \App\Services\FileUploadService $fileUploadService): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->except(['photo']));

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo_url) {
                $fileUploadService->delete($user->photo_url);
            }
            // Upload foto baru ke folder 'avatars'
            $user->photo_url = $fileUploadService->uploadVehiclePhoto($request->file('photo'), 'avatars');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's notification preferences.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->user()->update([
            'enable_service_reminders' => $request->boolean('enable_service_reminders'),
            'enable_email_notifications' => $request->boolean('enable_email_notifications'),
        ]);

        return Redirect::route('profile.edit')->with('status', 'notifications-updated');
    }
}
