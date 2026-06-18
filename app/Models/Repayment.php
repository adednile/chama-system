<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repayment extends Model
{
    protected $fillable = [
        'loan_id',
        'repayment_amount',   // ✅ correct
        'repayment_date',     // ✅ correct
        'remaining_balance',
        'is_late',
    ];

    protected $casts = [
        'repayment_amount' => 'decimal:2',   // ✅ fixed
        'repayment_date' => 'date',
        'remaining_balance' => 'decimal:2',
        'is_late' => 'boolean',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}