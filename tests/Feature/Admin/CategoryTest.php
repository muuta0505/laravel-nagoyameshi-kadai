<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_cannot_access_admin_category_index()
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_category_index() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_category_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/categories');
        $response->assertStatus(200);
    }
    // storeアクション（店舗登録機能）
    public function test_guest_cannot_store_admin_category()
    {
        $response = $this->post('/admin/categories', [], ['X-CSRF-TOKEN' => csrf_token()]);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_store_admin_category()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/admin/categories', [], ['X-CSRF-TOKEN' => csrf_token()]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_store_admin_category()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $category_data = [
            'name' => 'テスト2',
        ];

        $response = $this->actingAs($admin, 'admin')->post('/admin/categories', $category_data, ['X-CSRF-TOKEN' => csrf_token()]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', $category_data);

    }

    // updateアクション（店舗更新機能）
    public function test_guest_cannot_update_admin_category()
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_update_admin_category()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/admin/categories/1', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_update_admin_category()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $category = Category::factory()->create();

        $new_category_data = [
            'name' => 'テスト2',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.categories.update', $category), $new_category_data);

        $this->assertDatabaseHas('categories',$new_category_data);
    }


    // destroyアクション（店舗削除機能）
    public function test_guest_cannot_destroy_admin_category()
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_destroy_admin_category()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/admin/categories/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_destroy_admin_category()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Category::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->delete('/admin/categories/1');
        $response->assertStatus(302);
        $response->assertRedirect('/admin/categories');
    }

}


