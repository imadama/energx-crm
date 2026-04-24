<?php

namespace App\Services;

use App\Models\Offerte;

class TokenResolver
{
    private array $waarden = [];

    public function __construct(Offerte $offerte)
    {
        $klant   = $offerte->klant;
        $bedrijf = config('app.bedrijf', []);

        $this->waarden = [
            'klant.naam'        => $klant->naam ?? '',
            'klant.email'       => $klant->email ?? '',
            'klant.telefoon'    => $klant->telefoon ?? '',
            'klant.straat'      => $klant->straat ?? '',
            'klant.huisnummer'  => $klant->huisnummer ?? '',
            'klant.postcode'    => $klant->postcode ?? '',
            'klant.stad'        => $klant->stad ?? '',
            'klant.adres'       => trim(($klant->straat ?? '') . ' ' . ($klant->huisnummer ?? '') . ', ' . ($klant->postcode ?? '') . ' ' . ($klant->stad ?? '')),
            'offerte.nummer'    => $offerte->nummer ?? '',
            'offerte.datum'     => $offerte->created_at?->format('d-m-Y') ?? '',
            'offerte.geldig_tot'=> $offerte->geldig_tot?->format('d-m-Y') ?? '',
            'offerte.totaal'    => '€ ' . number_format((float) $offerte->totaal, 2, ',', '.'),
            'bedrijf.naam'      => $bedrijf['naam'] ?? 'Energx B.V.',
            'bedrijf.email'     => $bedrijf['email'] ?? 'info@energx.nl',
            'bedrijf.telefoon'  => $bedrijf['telefoon'] ?? '',
            'bedrijf.website'   => $bedrijf['website'] ?? 'www.energx.nl',
        ];
    }

    public function resolve(string $html): string
    {
        foreach ($this->waarden as $token => $waarde) {
            $html = str_replace(
                ['{{ ' . $token . ' }}', '{{' . $token . '}}'],
                e($waarde),
                $html
            );
        }

        // Vervang ook token-spans die door de editor ingevoegd zijn
        $html = preg_replace_callback(
            '/<span[^>]+data-token="([^"]+)"[^>]*>.*?<\/span>/s',
            function ($match) {
                $token = $match[1];
                return e($this->waarden[$token] ?? '[' . $token . ']');
            },
            $html
        );

        return $html;
    }

    public static function beschikbareTokens(): array
    {
        return [
            'Klant' => [
                ['token' => 'klant.naam',       'label' => 'Naam'],
                ['token' => 'klant.email',      'label' => 'E-mailadres'],
                ['token' => 'klant.telefoon',   'label' => 'Telefoon'],
                ['token' => 'klant.adres',      'label' => 'Volledig adres'],
                ['token' => 'klant.postcode',   'label' => 'Postcode'],
                ['token' => 'klant.stad',       'label' => 'Stad'],
            ],
            'Offerte' => [
                ['token' => 'offerte.nummer',     'label' => 'Offertenummer'],
                ['token' => 'offerte.datum',      'label' => 'Datum'],
                ['token' => 'offerte.geldig_tot', 'label' => 'Geldig tot'],
                ['token' => 'offerte.totaal',     'label' => 'Totaalbedrag'],
            ],
            'Bedrijf' => [
                ['token' => 'bedrijf.naam',     'label' => 'Bedrijfsnaam'],
                ['token' => 'bedrijf.email',    'label' => 'E-mail'],
                ['token' => 'bedrijf.telefoon', 'label' => 'Telefoon'],
                ['token' => 'bedrijf.website',  'label' => 'Website'],
            ],
        ];
    }
}
