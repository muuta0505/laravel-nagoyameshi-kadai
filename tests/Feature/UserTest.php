<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_cannot_access_users_index()
    {
        $response = $this->get('/user');
        $response->assertRedirect('/login');
    }
    public function test_user_cannot_access_users_index() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_users_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        User::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get('/user');
        $response->assertRedirect('/admin/home');
    }

    public function test_guest_cannot_access_users_edit()
    {
        User::factory()->create(['id' => 1]);
        $response = $this->get('/user/1/edit');
        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_others_users_edit()
    {
        $user = User::factory()->create(['id' => 1]);
        User::factory()->create(['id' => 2]);
        $response = $this->actingAs($user)->get('/user/2/edit');
        $response->assertRedirect('/user');
    }

    public function test_user_cannot_access_users_edit() 
    {
        $user = User::factory()->create(['id' => 1]);
        $response = $this->actingAs($user)->get('/user/1/edit');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_users_edit()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        User::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/user/1/edit');
        $response->assertRedirect('/admin/home');
    }

    public function test_guest_cannot_access_users_update()
    {
        User::factory()->create(['id' => 1]);
        $response = $this->put('/user/1',[]);
        $response->assertRedirect('/login');
    }
    public function test_user_cannot_access_others_users_update() 
    {
        $user = User::factory()->create(['id' => 1]);
        User::factory()->create(['id' => 2]);
        $new_users = [
            'name' => 'TEST2',
            'kana'=> 'テスト',
            'email'=> 'test@example.com',
            'postal_code' => '0111111',
            'address' => 'テスト',
            'phone_number' => '1234567890',
            'birthday' => '19991231',
            'occupation' => 'テスト',
        ];

        $response = $this->actingAs($user)->put('/user/2', $new_users);
        $this->assertDatabaseMissing('users', $new_users);
        $response->assertRedirect('/user');
    }

    public function test_user_cannot_access_users_update() 
    {
        $user = User::factory()->create(['id' => 1]);
        $new_users = [
            'name' => 'TEST2',
            'kana'=> 'テスト',
            'email'=> 'test@example.com',
            'postal_code' => '0111111',
            'address' => 'テスト',
            'phone_number' => '1234567890',
            'birthday' => '19991231',
            'occupation' => 'テスト',
        ];

        $this->actingAs($user)->put('/user/1', $new_users);
        $this->assertDatabaseHas('users', $new_users);
    }

    public function test_admin_can_access_users_update()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        User::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->patch('/user/1', []);
        $response->assertRedirect('/admin/home');
    }
}
