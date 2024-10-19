<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Term;

class TermTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_cannot_access_admin_terms_index()
    {
        $response = $this->get('/admin/terms');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_terms_index() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/terms');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_terms_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Term::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/terms');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_terms_edit()
    {
        $response = $this->get('/admin/terms/1/edit');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_terms_edit() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/terms/1/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_terms_edit()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Term::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/terms/1/edit');
        $response->assertStatus(200);
    }
    public function test_guest_cannot_access_admin_terms_update()
    {
        Term::factory()->create(['id' => 1]);
        $response = $this->put('/admin/terms/1',[]);
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_terms_update() 
    {
        $user = User::factory()->create();
        Term::factory()->create(['id' => 1]);

        $response = $this->actingAs($user)->put('/admin/terms/1', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_terms_update()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Term::factory()->create(['id' => 1]);
        $new_terms = [
            'content' => 'TEST2',
        ];
        $this->actingAs($admin, 'admin')->patch('/admin/terms/1', $new_terms);
        $this->assertDatabaseHas('terms', $new_terms);
    }
}
