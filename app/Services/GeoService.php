<?php

namespace App\Services;

class GeoService
{
    public function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        $a = sin($dLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($dLon / 2) ** 2;

        return $earthRadius * (2 * asin(min(1, sqrt($a))));
    }

    public function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        return $this->distanceMeters($lat1, $lon1, $lat2, $lon2) / 1000;
    }
}
