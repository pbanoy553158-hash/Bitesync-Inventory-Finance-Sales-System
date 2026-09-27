<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_profile_edit_page(): void
    {
        $user = User::factory()->create([
            'role' => 'Procurement',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Edit Profile')
            ->assertSee($user->email);
    }

    public function test_authenticated_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'role' => 'Procurement',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'current_password' => '',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::factory()->create([
            'role' => 'Procurement',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => '',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('current_password');
    }
}
