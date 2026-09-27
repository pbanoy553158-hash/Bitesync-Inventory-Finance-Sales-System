<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo as PurchaseRelation;

class Expense extends Model
{
    use HasFactory;

    /**
     * Fields that can be mass assigned.
     */
    protected $fillable = [
        'user_id',
        'category',
        'amount',
        'expense_date',
        'description',
        'reference_no',
        'status',
    ];

    /**
     * Automatically convert database values
     * into the appropriate PHP types.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * The user who recorded this expense.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Backward-compatible alias used by the original expense list.
     */
    public function getExpenseNoAttribute(): string
    {
        return sprintf('EXP-%06d', $this->id);
    }

    /**
     * Backward-compatible alias used by the original expense list.
     */
    public function getRecordedByAttribute(): string
    {
        return $this->user?->name
            ?? $this->user?->full_name
            ?? '—';
    }

    /**
     * Backward-compatible placeholder used by legacy list markup.
     */
    public function getPurchaseNoAttribute(): ?string
    {
        return null;
    }

    /**
     * Legacy relation expected by the old list markup.
     */
    public function purchase(): PurchaseRelation
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
}