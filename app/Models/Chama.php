<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chama extends Model
{
    protected $fillable = [
        'name',
        'location',
        'description',
        'currency',
        'contribution_target',
        'collection_cutoff',
        'late_penalty_flat',
        'interest_rate_pct',
        'min_credit_score',
        'savings_weight',
        'attendance_weight',
        'repayment_weight',
    ];

   public function users()
{
    return $this->hasMany(User::class);
}

public function loans()
{
    return $this->hasMany(Loan::class);
}

public function contributions()
{
    return $this->hasMany(Contribution::class);
}

public function fines()
{
    return $this->hasMany(Fine::class);
}

public function meetings()
{
    return $this->hasMany(Meeting::class);
}
}
