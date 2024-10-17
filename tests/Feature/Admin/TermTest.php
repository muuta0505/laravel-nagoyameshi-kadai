<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Term;
use Illuminate\Support\Facades\Hash;

class TermTest extends TestCase
{
    use RefreshDatabase;


public function test_guest_can_access_term_index():void
{
    $term = Term::factory()->create();

    $response = $this->get(route('terms.index'));
    $response->assertStatus(200);
}    


public function test_user_can_access_term_index():void
{
    $term = Term::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('terms.index'));
    $response->assertStatus(200);
}


public function test_admin_cannot_access_term_index():void
{
    $admin = Admin::create([
        'email' => 'admin@example.com',
        'password' => Hash::make('nagoyameshi'),
    ]);
    $term = Term::factory()->create();

    $response = $this->actingAs($admin,'admin')->get(route('terms.index'));
    $response->assertRedirect(route('admin.home'));

    }
}
