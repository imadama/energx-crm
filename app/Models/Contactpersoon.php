<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contactpersoon extends Model
{
    protected $table = 'contactpersonen';

    protected $fillable = [
        'klant_id',
        'voornaam',
        'achternaam',
        'email',
        'telefoon',
    ];

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class);
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
