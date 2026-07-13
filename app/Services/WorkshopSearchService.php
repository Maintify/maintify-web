<?php

namespace App\Services;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\DB;

class WorkshopSearchService
{
    /**
     * Search workshops nearby using geospatial coordinates.
     */
    public function search(array $params): Collection
    {
        $latitude = $params['latitude'] ?? null;
        $longitude = $params['longitude'] ?? null;
        $radius = $params['radius'] ?? 10; // default 10 km

        $minRating = $params['rating'] ?? null;
        $serviceType = $params['service_type'] ?? null;
        $status = $params['status'] ?? Workshop::STATUS_APPROVED;
        $isActive = $params['is_active'] ?? true;

        // Register custom math functions in SQLite connection (for local/test databases)
        $connection = DB::connection();
        if ($connection instanceof SQLiteConnection) {
            $pdo = $connection->getPdo();
            $pdo->sqliteCreateFunction('acos', 'acos', 1);
            $pdo->sqliteCreateFunction('cos', 'cos', 1);
            $pdo->sqliteCreateFunction('sin', 'sin', 1);
            $pdo->sqliteCreateFunction('radians', 'deg2rad', 1);
        }

        $query = Workshop::query();

        // 1. Basic Filters
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($minRating) {
            $query->where('rating_average', '>=', $minRating);
        }

        // 2. Filter by service type (workshops having performed this type of service)
        if ($serviceType) {
            $query->whereHas('serviceRecords', function ($q) use ($serviceType) {
                $q->where('service_type', $serviceType);
            });
        }

        // 3. Geospatial calculation using Haversine formula
        if ($latitude !== null && $longitude !== null) {
            $query->selectRaw('
                workshops.*,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ', [$latitude, $longitude, $latitude]);

            $query->orderBy('distance', 'asc');
        } else {
            $query->orderBy('rating_average', 'desc');
        }

        $results = $query->get();

        if ($latitude !== null && $longitude !== null) {
            $results = $results->filter(function ($workshop) use ($radius) {
                $workshop->distance = (float) $workshop->distance;

                return $workshop->distance <= $radius;
            })->values();
        }

        return $results;
    }
}
