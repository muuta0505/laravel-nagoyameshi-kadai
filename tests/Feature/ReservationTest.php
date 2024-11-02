<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;

class ReservationTest extends TestCase
{
    use RefreshDatabase;
   

public function test_guest_cannot_access_reservation_index():void
{
    $response = $this->get(route('reservations.index'));
    $response->assertRedirect(route('login'));
}


public function test_user_cannot_access_reservation_index():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $response = $this->actingAs($user)->get(route('reservations.index'));
    $response->assertRedirect(route('subscription.create'));
}


public function test_membership_user_can_access_reservation_index():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

    $response = $this->actingAs($user)->get(route('reservations.index'));
    $response->assertStatus(200);
}


public function test_admin_cannot_access_reservation_index():void
{
    $admin = Admin::create([
        'email' => 'admin@example.com',
        'password' => Hash::make('nagoyameshi'),
    ]);

    $response = $this->actingAs($admin,'admin')->get(route('reservations.index'));

    $response->assertRedirect(route('admin.home'));
}


public function test_guest_cannot_access_reservation_create():void
{
    $restaurant = Restaurant::factory()->create();
    $response = $this->get(route('restaurants.reservations.create',$restaurant));
    $response->assertRedirect(route('login'));
}


public function test_user_cannot_access_reservation_create():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $response = $this->actingAs($user)->get(route('restaurants.reservations.create',$restaurant));
    $response->assertRedirect(route('subscription.create'));
}


public function test_membership_user_can_access_reservation_create():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

    $response = $this->actingAs($user)->get(route('restaurants.reservations.create',$restaurant));
    $response->assertStatus(200);
}


public function test_admin_cannot_access_reservation_create():void
{
    $admin = Admin::create([
        'email' => 'admin@example.com',
        'password' => Hash::make('nagoyameshi'),
    ]);

    $restaurant = Restaurant::factory()->create();
    $response = $this->actingAs($admin,'admin')->get(route('restaurants.reservations.create',$restaurant));

    $response->assertRedirect(route('admin.home'));
}


public function test_guest_cannot_reservation():void
{
    $restaurant = Restaurant::factory()->create();
    $reservationData = [
        'reserved_datetime' => now(),
        'number_of_people' => 4,
        'restaurant_id' => 11,
    ];

    $response = $this->post(route('restaurants.reservations.store',$restaurant),$reservationData);
    $response->assertRedirect('login');
}


public function test_user_cannot_reservation():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $reservationData = [
        'reserved_datetime' => now(),
        'number_of_people' => 4,
        'restaurant_id' => 11,
    ];
    $response = $this->actingAs($user)->post(route('restaurants.reservations.store',$restaurant),$reservationData);

    $response->assertRedirect(route('subscription.create'));
}


public function test_membership_user_can_reservation():void
{
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

    $restaurant = Restaurant::factory()->create();

        $reservation_data = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];

        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation_data);

        $this->assertDatabaseHas('reservations', ['reserved_datetime' => '2024-01-01 00:00', 'number_of_people' => 10]);
        $response->assertRedirect(route('reservations.index',$restaurant));
    }


public function test_admin_cannot_reservation():void
{
    $admin = Admin::create([
        'email' => 'admin@example.com',
        'password' => Hash::make('nagoyameshi'),
    ]);

    $restaurant = Restaurant::factory()->create();

    $reservationData = [
        'reserved_datetime' => now(),
        'number_of_people' => 4,
        'restaurant_id' => 11,
    ];

    $response = $this->actingAs($admin,'admin')->post(route('restaurants.reservations.store',$restaurant),$reservationData);

    $response->assertRedirect(route('admin.home'));
}


public function test_guest_cannot_reservation_cancel():void
{
    
    $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('login'));
    }


public function test_user_cannot_reservation_cancel():void
{
    
    $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('subscription.create'));
    }


public function test_membership_user_cannot_cancel_others_reservation()
 {
     $user = User::factory()->create();
     $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

     $other_user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
 
    $other_user_restaurant_date = Reservation::factory()->create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $other_user->id
    ]);
 
    $response = $this->actingAs($user)->delete(route('reservations.destroy', $other_user_restaurant_date));
 
    $this->assertDatabaseHas('reservations', ['id' => $other_user_restaurant_date->id]);
    $response->assertRedirect(route('reservations.index'));
     }


public function test_membership_user_can_cancel_reservation()
{
    $user =User::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

    $restaurant = Restaurant::factory()->create();
    $reservation = Reservation::factory()->create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id
    ]);

    $response = $this->actingAs($user)->delete(route('reservations.destroy',$reservation));
    $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    $response->assertRedirect(route('reservations.index'));
}


public function test_admin_cannot_reservation_cancel():void
{
    $admin = Admin::create([
        'email' => 'admin@example.com',
        'password' => Hash::make('nagoyameshi'),
    ]);

    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $reservation = Reservation::factory()->create([
         'restaurant_id' => $restaurant->id,
         'user_id' => $user->id
        ]);

    $response = $this->actingAs($admin,'admin')->delete(route('reservations.destroy', $reservation));

    $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
     $response->assertRedirect(route('admin.home'));
    }
}