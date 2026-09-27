<?php

namespace Tests\Unit;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseAccessorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_original_expense_index_contract_is_available(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
        ]);

        $expense = Expense::create([
            'user_id' => $user->id,
            'category' => 'Utilities',
            'amount' => 420.50,
            'expense_date' => '2026-09-27',
            'description' => 'Office utilities',
            'reference_no' => 'REF-1001',
            'status' => 'Recorded',
        ]);

        $this->assertSame(sprintf('EXP-%06d', $expense->id), $expense->expense_no);
        $this->assertSame('Jane Doe', $expense->recorded_by);
        $this->assertNull($expense->purchase_no);
    }
}
