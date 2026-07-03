<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MappedMpesaTransaction extends Model
{
    protected $table = 'mapped_mpesa_transactions';

    protected $fillable = [
        'user_id',
        'amount',
        'sender',
        'transaction_code',
        'message',
        'status',
        'payment_type',
        'loan_id',
        'fine_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function fine(): BelongsTo
    {
        return $this->belongsTo(Fine::class);
    }
}
