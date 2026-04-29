<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReactie extends Model
{
    protected $table = 'ticket_reacties';

    protected $fillable = [
        'ticket_id',
        'type',
        'gebruiker_id',
        'bron',
        'inhoud',
        'bijlagen',
    ];

    protected $casts = [
        'bijlagen' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
