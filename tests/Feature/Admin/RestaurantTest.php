<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_cannot_access_admin_restaurant_index()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_restaurant_index() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_restaurant_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');
        $response->assertStatus(200);
    }
    public function test_guest_cannot_access_admin_restaurant_show()
    {
        $response = $this->get('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_restaurant_show() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_restaurant_show()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/1');
        $response->assertStatus(200);
    }
    public function test_guest_cannot_access_admin_restaurant_create()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_restaurant_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_restaurant_create()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/create');
        $response->assertStatus(200);
    }


    // storeアクション（店舗登録機能）
    public function test_guest_cannot_store_admin_restaurant()
    {
        $response = $this->post('/admin/restaurants', [], ['X-CSRF-TOKEN' => csrf_token()]);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_store_admin_restaurant()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/admin/restaurants', [], ['X-CSRF-TOKEN' => csrf_token()]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_store_admin_restaurant()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $categories = Category::factory()->count(3)->create();
        $category_ids = $categories->pluck('id')->toArray();

        $restaurant_data = [
            'name' => 'テスト2',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00',
            'closing_time' => '20:00',
            'seating_capacity' => 50,
            'category_ids' => $category_ids,
        ];

        $response = $this->actingAs($admin, 'admin')->post('/admin/restaurants', $restaurant_data, ['X-CSRF-TOKEN' => csrf_token()]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/restaurants');
        
        unset($restaurant_data['category_ids']);

        $this->assertDatabaseHas('restaurants', $restaurant_data);

        foreach ($category_ids as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }
    }

    // editアクション（店舗編集ページ）
    public function test_guest_cannot_access_admin_restaurant_edit()
    {
        $response = $this->get('/admin/restaurants/1/edit');
        
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_access_admin_restaurant_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants/1/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_restaurant_edit()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/1/edit');
        $response->assertStatus(200);
    }

    // updateアクション（店舗更新機能）
    public function test_guest_cannot_update_admin_restaurant()
    {
        $response = $this->put('/admin/restaurants/1', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_update_admin_restaurant()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/admin/restaurants/1', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_update_admin_restaurant()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $categories = Category::factory()->count(3)->create();
        $category_ids = $categories->pluck('id')->toArray();
        $restaurant = Restaurant::factory()->create();

        $new_restaurant_data = [
            'name' => 'テスト2',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00',
            'closing_time' => '20:00',
            'seating_capacity' => 50,
            'category_ids' => $category_ids
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.restaurants.update', $restaurant), $new_restaurant_data);

        unset($new_restaurant_data['category_ids']);

        $this->assertDatabaseHas('restaurants',$new_restaurant_data);
        foreach ($category_ids as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

    }


    // destroyアクション（店舗削除機能）
    public function test_guest_cannot_destroy_admin_restaurant()
    {
        $response = $this->delete('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_destroy_admin_restaurant()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_destroy_admin_restaurant()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->delete('/admin/restaurants/1');
        $response->assertStatus(302);
        $response->assertRedirect('/admin/restaurants');
    }

}