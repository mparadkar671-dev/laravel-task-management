<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_login_page_displays_forgot_password_option(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Forgot Password?')
            ->assertSee(route('password.request'));
    }

    public function test_guest_can_view_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200)
            ->assertSee('Forgot Password')
            ->assertSee('Work Email')
            ->assertSee('Send Reset Verification Link');
    }

    public function test_authenticated_user_is_redirected_from_forgot_password(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $response = $this->actingAs($user)->get('/forgot-password');

        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_request_password_reset_link_with_registered_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'employee@company.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'employee@company.com',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('status', __('passwords.sent'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_cannot_request_password_reset_link_with_unregistered_email(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'unknown@company.com',
        ]);

        $response->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    public function test_guest_can_view_reset_password_page_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@company.com',
        ]);
        $token = Password::createToken($user);

        $response = $this->get("/reset-password/{$token}?email=employee@company.com");

        $response->assertStatus(200)
            ->assertSee('Reset Password')
            ->assertSee('employee@company.com')
            ->assertSee($token);
    }

    public function test_user_can_reset_password_and_email_is_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@company.com',
            'password' => Hash::make('OldSecretPass123!'),
            'email_verified_at' => null,
        ]);
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'employee@company.com',
            'password' => 'NewStrongPass123!',
            'password_confirmation' => 'NewStrongPass123!',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('NewStrongPass123!', $user->password));
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@company.com',
            'password' => Hash::make('OldSecretPass123!'),
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token-string',
            'email' => 'employee@company.com',
            'password' => 'NewStrongPass123!',
            'password_confirmation' => 'NewStrongPass123!',
        ]);

        $response->assertSessionHasErrors('email');

        $user->refresh();
        $this->assertTrue(Hash::check('OldSecretPass123!', $user->password));
    }

    public function test_user_cannot_reset_password_with_weak_password(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@company.com',
        ]);
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'employee@company.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_api_v1_can_request_forgot_password_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'api_employee@company.com',
        ]);

        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => 'api_employee@company.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => __('passwords.sent'),
            ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_api_v1_can_reset_password_with_token(): void
    {
        $user = User::factory()->create([
            'email' => 'api_employee@company.com',
            'password' => Hash::make('OldSecretPass123!'),
        ]);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/v1/reset-password', [
            'token' => $token,
            'email' => 'api_employee@company.com',
            'password' => 'ApiNewStrongPass123!',
            'password_confirmation' => 'ApiNewStrongPass123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => __('passwords.reset'),
            ]);

        $this->assertTrue(Hash::check('ApiNewStrongPass123!', $user->fresh()->password));
    }

    public function test_admin_can_send_password_reset_link_to_any_team_member(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $employee = User::factory()->create([
            'email' => 'staff_member@company.com',
        ]);
        $employee->assignRole('employee');

        $response = $this->actingAs($admin)->post("/admin/users/{$employee->id}/send-reset-link");

        $response->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($employee, ResetPassword::class);
    }
}
