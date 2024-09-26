<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** テスト: 未ログインのユーザーは管理者側の会員一覧ページにアクセスできない */
    public function test_guest_user_cannot_access_admin_users_index()
    {
        $response = $this->get('admin/users');
        $response->assertRedirect('admin/login');
    }

    /** テスト: ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない */
    public function test_non_admin_user_cannot_access_admin_users_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('admin/users');
        $response->assertRedirect('admin/login');
    }

    /** テスト: ログイン済みの管理者は管理者側の会員一覧ページにアクセスできる */
    public function test_admin_user_can_access_admin_users_index()
    {
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('admin/users');
        $response->assertStatus(200);
    }

    
    /** テスト: 未ログインのユーザーは管理者側の会員詳細ページにアクセスできない */
    public function test_guest_user_cannot_access_admin_user_show()
    {
        $user = User::factory()->create();
        $response = $this->get(route('admin.users.show', $user));
        $response->assertRedirect('admin/login');
    }

    /** テスト: ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない */
    public function test_non_admin_user_cannot_access_admin_user_show()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.users.show', $user));
        $response->assertRedirect('admin/login');
    }

    /** テスト: ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる */
    public function test_admin_user_can_access_admin_user_show()
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.show', $user));
        $response->assertStatus(200);
    }
}