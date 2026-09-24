<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guest_is_redirected_to_login_when_accessing_profile(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $user->assignRole('employee');

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200)
            ->assertSee('John Doe')
            ->assertSee('john@example.com')
            ->assertSee('EMPLOYEE');
    }

    public function test_user_can_update_profile_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $user->assignRole('employee');

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $response->assertSessionHas('status', 'Profile details updated successfully!');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    public function test_user_cannot_update_email_to_an_existing_email(): void
    {
        $otherUser = User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create(['email' => 'my@example.com']);
        $user->assignRole('employee');

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'My Name',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'my@example.com',
        ]);
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentSecret123!'),
        ]);
        $user->assignRole('employee');

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'CurrentSecret123!',
            'password' => 'BrandNewSecret456@',
            'password_confirmation' => 'BrandNewSecret456@',
        ]);

        $response->assertSessionHas('status', 'Your password has been changed securely!');
        $this->assertTrue(Hash::check('BrandNewSecret456@', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentSecret123!'),
        ]);
        $user->assignRole('employee');

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'WrongPassword999!',
            'password' => 'BrandNewSecret456@',
            'password_confirmation' => 'BrandNewSecret456@',
        ]);

        $response->assertSessionHasErrors(['current_password']);
        $this->assertTrue(Hash::check('CurrentSecret123!', $user->fresh()->password));
    }

    public function test_user_can_mark_single_notification_as_read(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        // Task creation automatically triggers TaskAssignedNotification via TaskObserver
        $task = Task::factory()->create(['assigned_to' => $user->id]);

        $notification = $user->unreadNotifications()->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($user)->post("/notifications/{$notification->id}/mark-read");

        $response->assertSessionHas('status', 'Notification marked as read.');
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        // Task creations automatically trigger notifications
        Task::factory()->create(['assigned_to' => $user->id]);
        Task::factory()->create(['assigned_to' => $user->id]);

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post('/notifications/mark-all-read');

        $response->assertSessionHas('status', 'All notifications marked as read.');
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }
}
