<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klant extends Model
{
    protected $table = 'klanten';

    protected $fillable = [
        'naam', 'email', 'telefoon', 'straat', 'huisnummer',
        'postcode', 'stad', 'notities', 'bron',
    ];

    public function offertes(): HasMany
    {
        return $this->hasMany(Offerte::class);
    }

    public function getAdresAttribute(): string
    {
        return trim("{$this->straat} {$this->huisnummer}, {$this->postcode} {$this->stad}");
    }
}
