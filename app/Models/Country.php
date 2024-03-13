<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_code', 'code');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'country_code', 'code');
    }
}
