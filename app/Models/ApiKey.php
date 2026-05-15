<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $table = 'api_keys';

    protected $fillable = ['team_id', 'naam', 'key', 'actief'];

    protected $casts = ['actief' => 'boolean'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function generate(int $teamId, string $naam): self
    {
        return self::create([
            'team_id' => $teamId,
            'naam'    => $naam,
            'key'     => 'enx_' . Str::random(40),
            'actief'  => true,
        ]);
    }
}
