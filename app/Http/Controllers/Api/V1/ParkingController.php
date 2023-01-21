<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Parking;
use App\Http\Controllers\Controller;
use App\Http\Resources\ParkingResource;
use App\Services\ParkingPriceService;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    
    public function index()
    {
        return ParkingResource::collection(
            Parking::with([
                'zone', 
                'user', 
                'vehicle' => function ($query) {
                    $query->withoutGlobalScope('user');
                }]
            )->withoutGlobalScope('user')->get());
    }

    public function show(Parking $parking)
    {
        return ParkingResource::make($parking);
    }

    public function start(Request $request)
    {
        $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
        ]);

        $parking = Parking::create($request->only('zone_id', 'vehicle_id'));

        return ParkingResource::make($parking);
    }

    public function stop(Parking $parking)
    {
        $parking->update([
            'stop_time' => now(),
            'total_price' => ParkingPriceService::calculatePrice($parking->zone_id, $parking->start_time),
        ]);

        return ParkingResource::make($parking);
    }
}
