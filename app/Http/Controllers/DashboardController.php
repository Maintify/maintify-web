<?php

namespace App\Http\Controllers;

use App\Models\ServiceHistory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
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
            User::ROLE_WORKSHOP => $this->workshopDashboard($user),
            User::ROLE_SUPER_ADMIN => $this->superAdminDashboard(),
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

        $recentActivities = ServiceHistory::whereIn('vehicle_id', $vehicleIds)
            ->with(['vehicle', 'workshop'])
            ->orderByDesc('service_date')
            ->limit(5)
            ->get();

        $recentVehicles = $user->vehicles()->latest()->limit(3)->get();

        $totalServices = ServiceHistory::whereIn('vehicle_id', $vehicleIds)->count();

        return view('dashboard', compact(
            'totalVehicles',
            'avgHealthScore',
            'healthStatus',
            'upcomingService',
            'recentActivities',
            'recentVehicles',
            'totalServices',
        ));
    }

    /**
     * Build dashboard data for a Workshop user.
     */
    private function workshopDashboard(User $user): View
    {
        $workshop = $user->workshop;

        $totalServices = $workshop->serviceHistories()->count();

        $thisMonthServices = $workshop->serviceHistories()
            ->whereMonth('service_date', now()->month)
            ->whereYear('service_date', now()->year)
            ->count();

        $recentServices = $workshop->serviceHistories()
            ->with(['vehicle', 'vehicle.owner'])
            ->orderByDesc('service_date')
            ->limit(5)
            ->get();

        $activeCustomers = $workshop->serviceHistories()
            ->join('vehicles', 'service_histories.vehicle_id', '=', 'vehicles.id')
            ->distinct('vehicles.user_id')
            ->count('vehicles.user_id');

        return view('dashboard', compact(
            'workshop',
            'totalServices',
            'thisMonthServices',
            'recentServices',
            'activeCustomers',
        ));
    }

    /**
     * Build dashboard data for a Super Admin.
     */
    private function superAdminDashboard(): View
    {
        $totalUsers = User::where('role', User::ROLE_VEHICLE_OWNER)->count();

        $newUsersThisMonth = User::where('role', User::ROLE_VEHICLE_OWNER)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalVehicles = Vehicle::count();

        $totalWorkshops = Workshop::where('status', Workshop::STATUS_APPROVED)->count();

        $pendingWorkshops = Workshop::where('status', Workshop::STATUS_PENDING)->count();

        $totalServiceRecords = ServiceHistory::count();

        return view('dashboard', compact(
            'totalUsers',
            'newUsersThisMonth',
            'totalVehicles',
            'totalWorkshops',
            'pendingWorkshops',
            'totalServiceRecords',
        ));
    }
}
