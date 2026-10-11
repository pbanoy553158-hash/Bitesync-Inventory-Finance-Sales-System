<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_system_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings.edit'));

        $response->assertOk();
        $response->assertSee('data-theme="light"', false);
        $response->assertSee('data-text-size="medium"', false);
        $response->assertSee('Business Profile');
        $response->assertSee('Currency &amp; Locale', false);
        $response->assertSee('Appearance');
        $response->assertSee('Personalise how BiteSync looks for your account.');
        $response->assertSee('name="appearance_theme" value="dark"', false);
        $response->assertSee('name="text_size" value="large"', false);
        $response->assertSee('data-logout-dialog', false);
        $response->assertSee('Yes, log out');
        $response->assertSee('data-delete-dialog', false);
        $response->assertSee('data-delete-confirm', false);
        $response->assertSee('User Access');
    }

    public function test_admin_can_save_settings_and_values_apply_to_inventory_and_currency_display(): void
    {
        $admin = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);

        $response = $this->actingAs($admin)->put(
            route('admin.settings.update'),
            [
                'business_name' => 'BiteSync Test Kitchen',
                'business_email' => 'hello@example.test',
                'business_phone' => '555-0100',
                'business_address' => '1 Test Street',
                'currency_code' => 'USD',
                'timezone' => 'Asia/Manila',
                'locale' => 'en_PH',
                'appearance_theme' => 'dark',
                'text_size' => 'large',
                'default_minimum_stock' => '5.50',
            ]
        );

        $response->assertRedirect(route('admin.settings.edit'));
        $this->assertDatabaseHas('system_settings', [
            'key' => 'business_name',
            'value' => 'BiteSync Test Kitchen',
        ]);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'currency_code',
            'value' => 'USD',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'appearance_theme' => 'dark',
            'text_size' => 'large',
        ]);

        $inventoryCreate = $this->get(route('inventory.create'));

        $inventoryCreate->assertOk();
        $inventoryCreate->assertSee('data-theme="dark"', false);
        $inventoryCreate->assertSee('data-text-size="large"', false);
        $inventoryCreate->assertSee('value="5.50"', false);
        $inventoryCreate->assertSee('$', false);
        $this->assertSame('Asia/Manila', config('app.timezone'));
        $this->assertSame('en_PH', app()->getLocale());

        $anotherAdmin = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);

        $this->actingAs($anotherAdmin)
            ->get(route('inventory.create'))
            ->assertOk()
            ->assertSee('data-theme="light"', false)
            ->assertSee('data-text-size="medium"', false);
    }

    public function test_non_admin_cannot_view_system_settings(): void
    {
        $financeUser = User::factory()->create([
            'role' => 'Finance',
        ]);

        $this->actingAs($financeUser)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }
}