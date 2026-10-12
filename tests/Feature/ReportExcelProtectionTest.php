<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExcelProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_report_download_is_protected_from_normal_sheet_edits(): void
    {
        $user = User::factory()->create([
            'role' => 'CEO/Admin',
        ]);

        $response = $this->actingAs($user)
            ->get(route('reports.excel'));

        $response->assertOk();
        $response->assertHeader(
            'Content-Type',
            'application/vnd.ms-excel; charset=UTF-8'
        );
        $response->assertHeader(
            'Content-Disposition',
            'attachment; filename="BiteSync_Report_' . now()->startOfMonth()->format('Y-m-d') . '_to_' . now()->format('Y-m-d') . '.xls"'
        );
        $response->assertSee('<x:ProtectContents>True</x:ProtectContents>', false);
        $response->assertSee('<x:ProtectObjects>True</x:ProtectObjects>', false);
        $response->assertSee('<x:ProtectScenarios>True</x:ProtectScenarios>', false);
    }
}
