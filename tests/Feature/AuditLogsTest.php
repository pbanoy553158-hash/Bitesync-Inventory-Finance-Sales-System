<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_changes_are_recorded_and_visible_to_admins(): void
    {
        $admin = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'business_name' => 'Audit Test Business',
            'business_email' => '',
            'business_phone' => '',
            'business_address' => '',
            'currency_code' => 'PHP',
            'timezone' => 'UTC',
            'locale' => 'en',
            'appearance_theme' => 'light',
            'text_size' => 'medium',
            'default_minimum_stock' => 0,
        ])->assertRedirect(route('admin.settings.edit'));

        $auditLog = AuditLog::query()->firstOrFail();

        $this->assertSame($admin->id, $auditLog->user_id);
        $this->assertSame('admin.settings.update', $auditLog->route_name);
        $this->assertSame('Admin Settings Update', $auditLog->action);
        $this->assertSame(302, $auditLog->status_code);
        $this->assertDatabaseMissing('audit_logs', [
            'action' => 'Audit Test Business',
        ]);

        $response = $this->get(route('admin.audit-logs.index', [
            'search' => 'admin.settings.update',
        ]));

        $response->assertOk();
        $response->assertSee('Admin Settings Update');
        $response->assertSee('admin.settings.update');
    }

    public function test_audit_logs_page_renders_pagination_wrapper(): void
    {
        $admin = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);

        for ($i = 0; $i < 31; $i++) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'System Check ' . $i,
                'route_name' => 'system.check.' . $i,
                'subject_type' => 'SystemCheck',
                'subject_id' => $i + 1,
                'status_code' => 200,
                'ip_address' => '127.0.0.1',
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('audit-pagination-wrapper');
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $financeUser = User::factory()->create([
            'role' => 'Finance',
        ]);

        $this->actingAs($financeUser)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }
}
