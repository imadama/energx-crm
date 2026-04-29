<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'contactpersoon_id',
        'titel',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (!$ticket->nummer) {
                $jaar = date('Y');
                $laatste = static::where('nummer', 'like', "TCK-{$jaar}-%")->latest('id')->first();
                $volgnummer = $laatste ? intval(substr($laatste->nummer, -4)) + 1 : 1;
                $ticket->nummer = sprintf("TCK-%s-%04d", $jaar, $volgnummer);
            }
        });
    }

    public function contactpersoon(): BelongsTo
    {
        return $this->belongsTo(Contactpersoon::class);
    }

    public function reacties(): HasMany
    {
        return $this->hasMany(TicketReactie::class);
    }
}
