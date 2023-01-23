<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_create_vehicle()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('api/v1/vehicles', [
            'plate_number' => 'ABC123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.plate_number', 'ABC123');

        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'ABC123'
        ]);
    }

    public function test_user_can_view_only_vehicles_he_owns()
    {
        $john = User::factory()->create();
        $vehicleForJohn = Vehicle::factory()->create([
            'user_id' => $john->id,
        ]);
        
        $barry = User::factory()->create();
        $vehicleForBarry = Vehicle::factory()->create([
            'user_id' => $barry->id,
        ]);

        $response = $this->actingAs($john)->getJson('api/v1/vehicles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.0.plate_number', $vehicleForJohn->plate_number)
            ->assertJsonMissing($vehicleForBarry->toArray());
    }

    public function test_user_can_view_a_vehicle_he_owns()
    {
        $john = User::factory()->create();
        $vehicleForJohn = Vehicle::factory()->create([
            'user_id' => $john->id,
        ]);
    
        $response = $this->actingAs($john)->getJson('api/v1/vehicles/' . $vehicleForJohn->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.plate_number', $vehicleForJohn->plate_number);
    }

    public function test_user_cannot_view_a_vehicle_he_does_not_own()
    {
        $john = User::factory()->create();
        
        $barry = User::factory()->create();
        $vehicleForBarry = Vehicle::factory()->create([
            'user_id' => $barry->id,
        ]);

        $response = $this->actingAs($john)->getJson('api/v1/vehicles/' . $vehicleForBarry->id);

        $response->assertStatus(404);
    }

    public function test_user_can_update_a_vehicle_he_owns()
    {
        $john = User::factory()->create();
        $vehicleForJohn = Vehicle::factory()->create([
            'user_id' => $john->id,
        ]);

        $response = $this->actingAs($john)->putJson('api/v1/vehicles/' . $vehicleForJohn->id, [
            'plate_number' => 'A3356'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.plate_number', 'A3356');
    }

    public function test_user_cannot_update_a_vehicle_he_does_not_own()
    {
        $john = User::factory()->create();

        $barry = User::factory()->create();
        $vehicleForBarry = Vehicle::factory()->create([
            'user_id' => $barry->id,
        ]);

        $response = $this->actingAs($john)->putJson('api/v1/vehicles/' . $vehicleForBarry->id, [
            'plate_number' => 'A3356'
        ]);

        $response->assertStatus(404);
    }

    public function test_user_can_only_delete_a_vehicle_he_owns()
    {
        $john = User::factory()->create();
        $vehicleForJohn = Vehicle::factory()->create([
            'user_id' => $john->id,
        ]);

        $barry = User::factory()->create();
        $vehicleForBarry = Vehicle::factory()->create([
            'user_id' => $barry->id,
        ]);


        $response = $this->actingAs($john)->deleteJson('api/v1/vehicles/' . $vehicleForJohn->id);

        $response->assertStatus(204)
            ->assertNoContent();

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicleForJohn->id])
            ->assertDatabaseHas('vehicles', ['id' => $vehicleForBarry->id]);
    }

    public function test_user_cannot_delete_a_vehicle_he_does_not_own()
    {
        $john = User::factory()->create();

        $barry = User::factory()->create();
        $vehicleForBarry = Vehicle::factory()->create([
            'user_id' => $barry->id,
        ]);

        $response = $this->actingAs($john)->deleteJson('api/v1/vehicles/' . $vehicleForBarry->id);

        $response->assertStatus(404);

        $this->assertDatabaseHas('vehicles', ['id' => $vehicleForBarry->id]);
    }
}
