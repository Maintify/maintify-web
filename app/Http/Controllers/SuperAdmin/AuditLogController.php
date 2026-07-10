<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Tampilkan daftar log audit aktivitas sistem dengan filter pencarian dan tanggal.
     */
    public function index(Request $request): View
    {
        $actorSearch = $request->input('actor_search');
        $actionFilter = $request->input('action');
        $entityFilter = $request->input('entity_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = AuditLog::query()->with('actor');

        // Filter: Nama / Email Aktor
        if (!empty($actorSearch)) {
            $query->whereHas('actor', function ($q) use ($actorSearch) {
                $q->where('name', 'like', "%{$actorSearch}%")
                  ->orWhere('email', 'like', "%{$actorSearch}%");
            });
        }

        // Filter: Action Type
        if (!empty($actionFilter)) {
            $query->where('action', $actionFilter);
        }

        // Filter: Entity Type
        if (!empty($entityFilter)) {
            $query->where('entity_type', $entityFilter);
        }

        // Filter: Date Range
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Ambil opsi filter unik dari database
        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $entityTypes = AuditLog::select('entity_type')->whereNotNull('entity_type')->distinct()->pluck('entity_type');

        return view('super-admin.audit-logs.index', compact(
            'logs',
            'actions',
            'entityTypes',
            'actorSearch',
            'actionFilter',
            'entityFilter',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Tampilkan detail dari sebuah log audit (metadata & ip_address).
     */
    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('actor');

        return view('super-admin.audit-logs.show', compact('auditLog'));
    }
}
