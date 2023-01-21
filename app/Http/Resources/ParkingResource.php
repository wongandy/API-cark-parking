<?php

namespace App\Http\Resources;

use App\Services\ParkingPriceService;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $totalPrice = $this->total_price ?? ParkingPriceService::calculatePrice($this->zone_id, $this->start_time);
        
        return [
            'zone' => [
                'id' => $this->zone->id,
                'name' => $this->zone->name,
                'price_per_hour' => $this->zone->price_per_hour
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'vehicle' => [
                'id' => $this->vehicle->id,
                'plate_number' => $this->vehicle->plate_number,
            ],
            'id' => $this->id,
            'start_time' => $this->start_time->toDateTimeString(),
            'stop_time' => $this->stop_time?->toDateTimeString(),
            'total_price' => $totalPrice,
        ];
    }
}
