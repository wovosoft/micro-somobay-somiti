<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;

    public function nominee(): HasOne
    {
        return $this->hasOne(Nominee::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function getTotalDepositAttribute()
    {
        return $this->deposits()->sum("amount");
    }

    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class);
    }

    public function getTotalWithdrawAttribute()
    {
        return $this->withdraws()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->getTotalDepositAttribute() - $this->getTotalWithdrawAttribute();
    }
}
