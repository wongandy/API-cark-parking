<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ZoneTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_view_all_zones()
    {
        $john = User::factory()->create();

        $response = $this->actingAs($john)->getJson('api/v1/zones');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.0.name', 'Green Zone')
            ->assertJsonPath('data.0.price_per_hour', 100);
    }

    public function test_user_can_view_a_zone()
    {
        $john = User::factory()->create();
        $zone = Zone::first();
        $response = $this->actingAs($john)->getJson('api/v1/zones/' . $zone->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.name', 'Green Zone')
            ->assertJsonPath('data.price_per_hour', 100);
    }
}
