<?php

namespace App\Models;

use App\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Offerte extends Model
{
    protected $table = 'offertes';

    protected $fillable = [
        'team_id', 'klant_id', 'template_id', 'nummer', 'token', 'status', 'inleiding',
        'subtotaal', 'btw_bedrag', 'totaal', 'geldig_tot',
        'verstuurd_op', 'bekeken_op', 'geaccepteerd_op', 'geaccepteerd_door',
        'geaccepteerd_akkoord', 'geaccepteerd_handtekening_type', 'geaccepteerd_handtekening',
    ];

    protected $casts = [
        'geldig_tot'      => 'date',
        'verstuurd_op'    => 'datetime',
        'bekeken_op'      => 'datetime',
        'geaccepteerd_op' => 'datetime',
        'geaccepteerd_akkoord' => 'boolean',
        'subtotaal'       => 'decimal:2',
        'btw_bedrag'      => 'decimal:2',
        'totaal'          => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TeamScope());
        static::creating(function (Offerte $offerte) {
            if (empty($offerte->token)) {
                $offerte->token = Str::random(32);
            }
            if (empty($offerte->team_id) && auth()->check()) {
                $offerte->team_id = auth()->user()->team_id;
            }
            if (empty($offerte->nummer)) {
                $jaar = now()->year;
                $teamId = $offerte->team_id;
                $query = static::withoutGlobalScope(TeamScope::class)->whereYear('created_at', $jaar);
                if ($teamId) {
                    $query->where('team_id', $teamId);
                }
                $volgend = $query->count() + 1;
                $offerte->nummer = sprintf('ENX-%d-%04d', $jaar, $volgend);
            }
        });
    }

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class);
    }

    public function regels(): HasMany
    {
        return $this->hasMany(OfferteRegel::class)->orderBy('volgorde');
    }

    public function secties(): HasMany
    {
        return $this->hasMany(OfferteSectie::class)->orderBy('volgorde');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OfferteTemplate::class);
    }

    public function berekenTotalen(): void
    {
        $subtotaal = $this->regels()->sum('totaal');
        $this->update([
            'subtotaal'  => $subtotaal,
            'btw_bedrag' => round($subtotaal * 0.21, 2),
            'totaal'     => round($subtotaal * 1.21, 2),
        ]);
    }

    public function getViewerUrlAttribute(): string
    {
        return url("/offerte/{$this->token}");
    }
}
