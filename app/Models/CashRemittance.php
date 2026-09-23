<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRemittance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'remittance_date',
        'expected_amount',
        'actual_amount',
        'variance',
        'reference_no',
        'remarks',
        'status',
    ];

    protected $casts = [
        'remittance_date' => 'date',
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    /**
     * User who recorded the cash remittance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the remittance has no variance.
     */
    public function hasNoVariance(): bool
    {
        return abs((float) $this->variance) < 0.01;
    }

    /**
     * Determine if the remittance has a shortage.
     */
    public function hasShortage(): bool
    {
        return (float) $this->variance < 0;
    }

    /**
     * Determine if the remittance has an excess.
     */
    public function hasExcess(): bool
    {
        return (float) $this->variance > 0;
    }
}