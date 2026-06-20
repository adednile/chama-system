<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmortizationSchedule extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_no',
        'due_date',
        'principal_portion',
        'interest_portion',
        'balance_after',
        'payment_status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'principal_portion' => 'decimal:2',
        'interest_portion' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}