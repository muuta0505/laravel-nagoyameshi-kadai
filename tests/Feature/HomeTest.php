<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class HomeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_can_access_home_index()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    public function test_user_can_access_home_index() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_home_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/');
        $response->assertRedirect('/admin/home');
    }
}