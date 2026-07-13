<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Get the active workshop for the authenticated user.
     */
    private function getWorkshop(Request $request): ?Workshop
    {
        /** @var User $user */
        $user = $request->user();

        $workshop = $user->workshop;
        if ($workshop instanceof Workshop) {
            return $workshop;
        }

        $staffWorkshop = $user->workshopStaff?->workshop;

        return $staffWorkshop instanceof Workshop ? $staffWorkshop : null;
    }

    /**
     * Display a listing of the workshop's customers.
     */
    public function index(Request $request): View
    {
        $workshop = $this->getWorkshop($request);
        if (! $workshop) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');

        // Query Users who own vehicles that have service records in this workshop
        $query = User::where('role', User::ROLE_VEHICLE_OWNER)
            ->whereHas('vehicles.serviceRecords', function ($q) use ($workshop) {
                $q->where('workshop_id', $workshop->id);
            });

        // Search by customer name or vehicle plate number
        if ($search) {
            $query->where(function ($q) use ($search, $workshop) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhereHas('vehicles', function ($vq) use ($search, $workshop) {
                        $vq->where('plate_number', 'like', "%{$search}%")
                            ->whereHas('serviceRecords', function ($sq) use ($workshop) {
                                $sq->where('workshop_id', $workshop->id);
                            });
                    });
            });
        }

        // Subquery for the last service date at this workshop
        $query->select('users.*')
            ->selectSub(
                ServiceRecord::select('service_date')
                    ->join('vehicles', 'service_records.vehicle_id', '=', 'vehicles.id')
                    ->whereColumn('vehicles.user_id', 'users.id')
                    ->where('service_records.workshop_id', $workshop->id)
                    ->orderBy('service_date', 'desc')
                    ->limit(1),
                'last_service_date'
            )
            ->orderBy('last_service_date', 'desc');

        // Eager load only the vehicles serviced at this workshop
        $customers = $query->with(['vehicles' => function ($q) use ($workshop) {
            $q->whereHas('serviceRecords', function ($sq) use ($workshop) {
                $sq->where('workshop_id', $workshop->id);
            })->with(['serviceRecords' => function ($sq) use ($workshop) {
                $sq->where('workshop_id', $workshop->id)->latest('service_date');
            }]);
        }])->paginate(10)->withQueryString();

        return view('workshop.customers.index', compact('customers', 'search'));
    }
}
