<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'chama_id',
    'national_id', 'phone', 'is_verified', 'account_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function chama()
{
    return $this->belongsTo(Chama::class);
}

public function contributions()
{
    return $this->hasMany(Contribution::class);
}

public function loans()
{
    return $this->hasMany(Loan::class);
}

public function fines()
{
    return $this->hasMany(Fine::class);
}

public function transactions()
{
    return $this->hasMany(Transaction::class);
}

public function attendances()
{
    return $this->hasMany(Attendance::class);
}

public function meetings()
{
    return $this->belongsToMany(Meeting::class, 'attendances')
                ->wherePivot('present', true);
}


}
