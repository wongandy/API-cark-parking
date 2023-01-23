<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Parking;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_start_parking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $response = $this->actingAs($user)->postJson('api/v1/parkings/start', [
            'zone_id' => $zone->id,
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.zone.name', 'Green Zone')
            ->assertJsonPath('data.start_time', now()->toDateTimeString())
            ->assertJsonPath('data.stop_time', null)
            ->assertJsonPath('data.total_price', 0);
    }

    public function test_user_can_view_all_parkings()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $this->actingAs($user)->postJson('api/v1/parkings/start', [
            'zone_id' => $zone->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->actingAs($user)->getJson('api/v1/parkings');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.0.zone.name', 'Green Zone')
            ->assertJsonPath('data.0.start_time', now()->toDateTimeString())
            ->assertJsonPath('data.0.stop_time', null);
    }

    public function test_user_can_view_a_parking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $this->actingAs($user)->postJson('api/v1/parkings/start', [
            'zone_id' => $zone->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $parking = Parking::first();
        ;
        $this->travel(1)->minute();

        $response = $this->actingAs($user)->getJson('api/v1/parkings/' . $parking->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.zone.name', 'Green Zone')
            ->assertJsonPath('data.start_time', $parking->start_time->toDateTimeString())
            ->assertJsonPath('data.stop_time', null)
            ->assertJsonPath('data.total_price', 2);
    }

    public function test_user_can_stop_own_parking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $this->actingAs($user)->postJson('api/v1/parkings/start', [
            'zone_id' => $zone->id,
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $this->travel(1)->minutes();

        $parking = Parking::first();

        $response = $this->actingAs($user)->putJson('api/v1/parkings/stop/' . $parking->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.zone.name', 'Green Zone')
            ->assertJsonPath('data.stop_time', now()->toDateTimeString())
            ->assertJsonPath('data.total_price', 2);
    }

    public function test_user_cannot_stop_other_persons_parking()
    {
        $john = User::factory()->create();
        
        $barry = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $barry->id]);

        $this->actingAs($barry)->postJson('api/v1/parkings/start', [
            'zone_id' => Zone::first()->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $this->travel(1)->minutes();

        $parking = Parking::first();
        $response = $this->actingAs($john)->putJson('api/v1/parkings/stop/' . $parking->id);
        $response->assertStatus(404);
    }
}
