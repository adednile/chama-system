<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'chama_id',
        'amount',
        'interest_rate',
        'term_months',
        'approved_amount',
        'status',
        'reason',
        'approved_at',
        'repaid_at',
        'credit_score',
        'rejection_reason',
        'outstanding_balance',
        'maturity_date',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'approved_at' => 'datetime',
        'repaid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chama(): BelongsTo
    {
        return $this->belongsTo(Chama::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }

    public function amortizationSchedule(): HasMany
    {
        return $this->hasMany(AmortizationSchedule::class);
    }
}

