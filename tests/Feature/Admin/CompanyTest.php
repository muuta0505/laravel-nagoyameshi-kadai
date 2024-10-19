<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;

class CompanyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_cannot_access_admin_company_index()
    {
        $response = $this->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_company_index() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_company_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Company::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/company');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_company_edit()
    {
        $response = $this->get('/admin/company/1/edit');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_company_edit() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/company/1/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_company_edit()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Company::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/company/1/edit');
        $response->assertStatus(200);
    }
    public function test_guest_cannot_access_admin_company_update()
    {
        Company::factory()->create(['id' => 1]);
        $response = $this->put('/admin/company/1',[]);
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_company_update() 
    {
        $user = User::factory()->create();
        Company::factory()->create(['id' => 1]);

        $response = $this->actingAs($user)->put('/admin/company/1', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_company_update()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Company::factory()->create(['id' => 1]);
        $new_company = [
            'name' => 'TEST2',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
        ];

        $this->actingAs($admin, 'admin')->patch('/admin/company/1', $new_company);
        $this->assertDatabaseHas('companies', $new_company);
    }
}