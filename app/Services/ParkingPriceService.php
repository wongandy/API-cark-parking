<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\Parking;
use DateTime;

class ParkingPriceService
{
    public static function calculatePrice(int $zoneId, DateTime $startTime)
    {
        $stopTime = now();

        $totalMinutes = $stopTime->diffInMinutes($startTime);

        $pricePerMinute = Zone::find($zoneId)->price_per_hour / 60;

        return ceil($totalMinutes * $pricePerMinute);
    }
}