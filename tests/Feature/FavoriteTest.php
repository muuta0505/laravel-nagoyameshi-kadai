<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    public function test_guest_cannot_access_favorite_index():void
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('login'));
    }

    
    public function test_user_cannot_access_favorite_index():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertRedirect(route('subscription.create'));

    }

    
    public function test_membership_user_can_access_favorite_index():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response =$this->actingAs($user)->get(route('favorites.index'));
        $response->assertStatus(200);
}

    
    public function test_admin_cannot_access_favorite_index():void
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin,'admin')->get(route('favorites.index'));

        $response->assertRedirect(route('admin.home'));
    }

    
    public function test_guest_cannot_store_favorite():void
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('favorites.store', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('login'));
    }

    
    public function test_user_cannot_store_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('subscription.create'));
    }

    
    public function test_membership_user_can_store_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($user)->post(route('favorites.store',$restaurant->id));

        $this->assertDatabaseHas('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertStatus(302);
    }

    
    public function test_admin_cannot_store_favorite():void
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin,'admin')->post(route('favorites.store', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('admin.home'));
    }

    
    public function test_guest_cannot_delete_favorite():void
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('login'));
    }

    
    public function test_user_cannot_delete_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('subscription.create'));
    }

    
    public function test_membership_user_can_delete_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($user)->delete(route('favorites.destroy',$restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertStatus(302);
    }

    
    public function test_admin_cannot_delete_favorite():void
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $restaurant = Restaurant::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin,'admin')->delete(route('favorites.destroy', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('admin.home'));
    }
}

