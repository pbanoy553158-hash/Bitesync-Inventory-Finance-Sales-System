<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignupPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_signup_form(): void
    {
        $this->get('/signup')
            ->assertOk()
            ->assertSee('Create account')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee(route('login'));
    }

    public function test_signup_creates_a_pending_account_without_signing_the_user_in(): void
    {
        $response = $this->followingRedirects()->post('/signup', [
            'name' => 'New BiteSync User',
            'email' => 'new.user@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => 'CEO/Admin',
        ]);

        $user = User::where('email', 'new.user@example.com')->firstOrFail();

        $response->assertOk()
            ->assertSee('Wait for admin approval');
        $this->assertGuest();
        $this->assertSame('Pending', $user->role);
        $this->assertSame(User::APPROVAL_PENDING, $user->approval_status);
        $this->assertTrue(password_verify('secure-password', $user->password));
    }

    public function test_pending_user_cannot_sign_in(): void
    {
        User::factory()->create([
            'email' => 'pending@example.com',
            'password' => 'secure-password',
            'role' => 'Pending',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->from('/login')
            ->post('/login', [
                'email' => 'pending@example.com',
                'password' => 'secure-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Your account is waiting for admin approval.',
            ]);

        $this->assertGuest();
    }

    public function test_declined_user_cannot_sign_in(): void
    {
        User::factory()->create([
            'email' => 'declined@example.com',
            'password' => 'secure-password',
            'role' => 'Pending',
            'approval_status' => User::APPROVAL_DECLINED,
        ]);

        $this->from('/login')
            ->post('/login', [
                'email' => 'declined@example.com',
                'password' => 'secure-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Your account has not been approved.',
            ]);

        $this->assertGuest();
    }

    public function test_admin_can_approve_a_pending_user_and_assign_a_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'CEO/Admin',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);
        $pendingUser = User::factory()->create([
            'role' => 'Pending',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $pendingUser), [
                'role' => 'Finance',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'role' => 'Finance',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);
    }

    public function test_admin_can_decline_a_pending_user_without_deleting_the_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'CEO/Admin',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);
        $pendingUser = User::factory()->create([
            'role' => 'Pending',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.decline', $pendingUser))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', "{$pendingUser->name}'s account has been declined.");

        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'approval_status' => User::APPROVAL_DECLINED,
        ]);
    }

    public function test_admin_user_list_shows_a_decline_button_for_pending_accounts(): void
    {
        $admin = User::factory()->create([
            'role' => 'CEO/Admin',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);
        $pendingUser = User::factory()->create([
            'role' => 'Pending',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Decline')
            ->assertSee(route('admin.users.decline', $pendingUser));
    }

    public function test_signup_rejects_invalid_or_duplicate_accounts(): void
    {
        User::factory()->create([
            'email' => 'taken@example.com',
        ]);

        $this->from('/signup')
            ->post('/signup', [
                'name' => 'Another User',
                'email' => 'taken@example.com',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect('/signup')
            ->assertSessionHasErrors([
                'email',
                'password',
            ]);
    }
}
