<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_guest_cannot_access_admin_top():void
    {
        $response = $this->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));
    }

    
    public function test_user_cannot_access_admin_top():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));

    }

    
    public function test_admin_can_access_admin_top():void
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin,'admin')->get(route('admin.home'));
        $response->assertStatus(200);

    }
}

