<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Apply timeout only for super_admin and workshop roles
            if (in_array($user->role, ['super_admin', 'workshop'])) {
                $timeout = 1800; // 30 minutes in seconds
                $lastActivity = session('last_activity_time');
                
                if ($lastActivity && (time() - $lastActivity) > $timeout) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas.');
                }
                
                // Update last activity time
                session(['last_activity_time' => time()]);
            }
        }

        return $next($request);
    }
}
