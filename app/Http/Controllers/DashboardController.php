<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Detect user role and dispatch to the appropriate dashboard method.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return match ($user->role) {
            User::ROLE_VEHICLE_OWNER => $this->vehicleOwnerDashboard($user),
            User::ROLE_WORKSHOP => app(Workshop\DashboardController::class)->index(),
            User::ROLE_SUPER_ADMIN => app(SuperAdmin\DashboardController::class)->index(),
            default => $this->vehicleOwnerDashboard($user),
        };
    }

    /**
     * Build dashboard data for a Vehicle Owner.
     */
    private function vehicleOwnerDashboard(User $user): View
    {
        $vehicleIds = $user->vehicles()->pluck('id');

        $totalVehicles = $user->vehicles()->count();
        $avgHealthScore = $user->vehicles()->active()->avg('health_score');

        $healthStatus = match (true) {
            $avgHealthScore === null => 'none',
            $avgHealthScore >= 70 => 'good',
            $avgHealthScore >= 40 => 'warning',
            default => 'critical',
        };

        $upcomingService = $user->vehicles()
            ->whereNotNull('next_service_date')
            ->orderBy('next_service_date')
            ->first();

        $recentActivities = ServiceRecord::whereIn('vehicle_id', $vehicleIds)
            ->with(['vehicle', 'workshop'])
            ->orderByDesc('service_date')
            ->limit(5)
            ->get();

        $recentVehicles = $user->vehicles()->latest()->limit(3)->get();

        $totalServices = ServiceRecord::whereIn('vehicle_id', $vehicleIds)->count();

        $pendingTransfers = \App\Models\OwnershipTransfer::where('to_user_id', $user->id)
            ->where('status', \App\Models\OwnershipTransfer::STATUS_PENDING_RECIPIENT)
            ->with(['vehicle', 'fromUser'])
            ->get();

        return view('dashboard', compact(
            'totalVehicles',
            'avgHealthScore',
            'healthStatus',
            'upcomingService',
            'recentActivities',
            'recentVehicles',
            'totalServices',
            'pendingTransfers',
        ));
    }
}
