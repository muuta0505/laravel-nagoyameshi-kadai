<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_subscription_create()
    {
        $response = $this->get('/subscription/create');
        $response->assertRedirect('/login');
    }
    public function test_user_can_access_subscription_create() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/subscription/create');
        $response->assertStatus(200);
    }

    public function test_membership_user_cannot_access_subscription_create() 
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($user)->get('/subscription/create');
        $response->assertRedirect('/subscription/edit');
    }

    public function test_admin_cannot_access_subscription_create() 
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($admin, 'admin')->get('/subscription/create');
        $response->assertRedirect('/admin/home');
    }
    

    public function test_guest_cannot_access_subscription_store()
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->post('/subscription',$request_parameter);
        $response->assertRedirect('/login');
    }
    public function test_user_can_access_subscription_store() 
    {
        $user = User::factory()->create();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
    
        $response = $this->actingAs($user)->post('/subscription',$request_parameter);
    
        $response->assertRedirect('/');

        $user->refresh();
        $this->assertTrue($user->subscribed('premium_plan'));
    }

    public function test_membership_user_cannot_access_subscription_store() 
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
    
        $response = $this->actingAs($user)->post('/subscription',$request_parameter);

        $response->assertRedirect('/subscription/edit');
    }

    public function test_admin_cannot_access_subscription_store() 
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($admin,'admin')->post('/subscription',$request_parameter);
        $response->assertRedirect('/admin/home');
    }


    public function test_guest_cannot_access_subscription_edit()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->get('/subscription/edit');
        $response->assertRedirect('/login');
    }
    public function test_user_cannot_access_subscription_edit() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/subscription/edit');
        $response->assertRedirect('/subscription/create');
    }

    public function test_membership_user_can_access_subscription_edit() 
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($user)->get('/subscription/edit');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_subscription_edit() 
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($admin, 'admin')->get('/subscription/edit');
        $response->assertRedirect('/admin/home');
    }
    
public function test_guest_cannot_access_subscription_update()
{
    $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
    ];

    $response = $this->patch('subscription/update',$request_parameter);
    $response->assertRedirect('/login');
}
public function test_user_cannot_access_subscription_update() 
{
    $user = User::factory()->create();
    $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
    ];

    $response = $this->actingAs($user)->patch('subscription/update',$request_parameter);
    $response->assertRedirect('/subscription/create');
}

public function test_membership_user_can_access_subscription_update() 
{
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');
    $payment_method_id = $user->defaultPaymentMethod()->id;

    $request_parameter = [
        'paymentMethodId' => 'pm_card_mastercard'
    ];
    $response = $this->actingAs($user)->patch('subscription/update',$request_parameter);
    $response->assertRedirect('/');

    $user->refresh();
    $this->assertNotEquals($payment_method_id, $user->defaultPaymentMethod()->id);
}

public function test_admin_cannot_access_subscription_update() 
{
    $admin = Admin::create([
        'email' => 'admin@example.com',
        'password' => Hash::make('nagoyameshi'),
    ]);

    $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
    ];
    $response = $this->actingAs($admin,'admin')->patch('subscription/update',$request_parameter);
    $response->assertRedirect('/admin/home');
}

public function test_guest_cannot_access_subscription_cancel()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->get('/subscription/cancel');
        $response->assertRedirect('/login');
    }
    public function test_user_cannot_access_subscription_cancel() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/subscription/cancel');
        $response->assertRedirect('/subscription/create');
    }

    public function test_membership_user_can_access_subscription_cancel() 
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($user)->get('/subscription/cancel');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_subscription_cancel() 
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

        $response = $this->actingAs($admin, 'admin')->get('/subscription/cancel');
        $response->assertRedirect('/admin/home');
    }

    
public function test_guest_cannot_access_subscription_delete()
{
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

    $response = $this->delete('/subscription');
    $response->assertRedirect('/login');
}
public function test_user_cannot_access_subscription_delete() 
{
    $user = User::factory()->create();
    $response = $this->actingAs($user)->delete('/subscription');
    $response->assertRedirect('/subscription/create');
}

public function test_membership_user_can_access_subscription_delete() 
{
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

    $response = $this->actingAs($user)->delete('/subscription');
    $response->assertStatus(302);
    $response->assertRedirect('/');
}

public function test_admin_cannot_access_subscription_delete() 
{
    $admin = Admin::create([
        'email' => 'admin@example.com',
        'password' => Hash::make('nagoyameshi'),
    ]);

    $user = User::factory()->create();
    $user->newSubscription('premium_plan', env('STRIPE_ID'))->create('pm_card_visa');

    $response = $this->actingAs($admin, 'admin')->delete('/subscription');
    $response->assertRedirect('/admin/home');
}


}
