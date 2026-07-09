<?php

namespace App\Http\Middleware;

use App\Models\Workshop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkshopApprovedMiddleware
{
    /**
     * Redirect bengkel yang belum diapprove ke halaman pending.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isWorkshop()) {
            // Check if user is staff and deactivated
            if ($user->workshopStaff && !$user->workshopStaff->is_active) {
                auth()->logout();
                return redirect()->route('login')->withErrors(['email' => 'Akun staff Anda telah dinonaktifkan.']);
            }

            /** @var Workshop|null $workshop */
            $workshop = $user->workshop ?? $user->workshopStaff?->workshop;

            // Jika workshop tidak ada atau belum approved, redirect ke pending
            if (! $workshop || $workshop->status !== 'approved') {
                // Izinkan akses ke halaman pending & logout itu sendiri
                if (! $request->routeIs('workshop.pending', 'logout')) {
                    return redirect()->route('workshop.pending');
                }
            }
        }

        return $next($request);
    }
}
