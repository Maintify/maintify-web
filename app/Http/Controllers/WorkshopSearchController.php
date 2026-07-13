<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Services\WorkshopSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopSearchController extends Controller
{
    protected WorkshopSearchService $searchService;

    public function __construct(WorkshopSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display the nearby workshops map page.
     */
    public function index(): View
    {
        $serviceTypes = ServiceRecord::SERVICE_TYPES;

        return view('workshops.nearby', compact('serviceTypes'));
    }

    /**
     * Search for nearby workshops.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'service_type' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:pending,approved,rejected,revision_needed'],
        ]);

        if ($request->has('latitude') !== $request->has('longitude')) {
            return response()->json([
                'success' => false,
                'message' => 'Kedua koordinat (latitude dan longitude) harus diberikan bersamaan.',
            ], 422);
        }

        $params = $request->only([
            'latitude',
            'longitude',
            'radius',
            'rating',
            'service_type',
            'status',
        ]);

        // Only verified workshops returned to customers (FR-051)
        $user = $request->user();
        if (! $user || $user->role !== 'super_admin') {
            $params['status'] = 'approved';
        }

        $workshops = $this->searchService->search($params);

        return response()->json([
            'success' => true,
            'data' => $workshops,
        ]);
    }
}
