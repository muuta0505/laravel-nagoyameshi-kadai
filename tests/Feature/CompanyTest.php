<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;

class CompanyTest extends TestCase
{
    use RefreshDatabase;
    
    
    public function test_guest_can_access_company_index():void
    {
        $company = Company::factory()->create();
        $response = $this->get(route('company.index'));
        $response->assertStatus(200);
    }

    
    public function test_user_can_access_company_index():void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('company.index'));
        $response->assertStatus(200);
    }

    
    public function test_admin_cannot_access_company_index():void
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $company = Company::factory()->create();

        $response = $this->actingAs($admin,'admin')->get(route(('company.index')));
        $response->assertRedirect(route('admin.home'));
    }
}

