<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
class RestaurantTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_can_access_restaurants_index()
    {
        $response = $this->get('/restaurants');
        $response->assertStatus(200);
    }
    public function test_user_can_access_restaurants_index() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/restaurants');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_restaurants_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/restaurants');
        $response->assertRedirect('/admin/home');
    }

    public function test_guest_cannot_access_restaurants_show()
    {   
        Restaurant::factory()->create(['id' => 1]);
        $response = $this->get('/restaurants');
        $response->assertStatus(200);
    }
    public function test_user_cannot_access_restaurants_show() 
    {
        $user = User::factory()->create();
        Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($user)->get('/restaurants/1');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_restaurants_show()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/restaurants/1');
        $response->assertRedirect('/admin/home');
    }
}
