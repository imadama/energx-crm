<?php

namespace Database\Seeders;

use App\Models\ApiField;
use App\Models\OfferteTemplate;
use App\Models\OfferteTemplateSectie;
use App\Models\Team;
use Illuminate\Database\Seeder;

class AircoTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedApiFields();
        $this->seedTemplate();
    }

    private function seedApiFields(): void
    {
        $fields = [
            [
                'key'            => 'gebruik',
                'label'          => 'Primair gebruik airco',
                'type'           => 'list',
                'allowed_values' => ['Koelen in de zomer', 'Verwarmen in de winter', 'Koelen én verwarmen'],
            ],
            [
                'key'            => 'ruimtes',
                'label'          => 'Aantal ruimtes',
                'type'           => 'list',
                'allowed_values' => ['1 ruimte', '2–3 ruimtes', 'Hele woning'],
            ],
            [
                'key'            => 'oppervlakte',
                'label'          => 'Oppervlakte (m²)',
                'type'           => 'text',
                'allowed_values' => [],
            ],
            [
                'key'            => 'woningtype',
                'label'          => 'Woningtype',
                'type'           => 'list',
                'allowed_values' => ['Appartement', 'Tussenwoning', 'Hoekwoning', 'Vrijstaand'],
            ],
            [
                'key'            => 'etage',
                'label'          => 'Verdieping binnenunit',
                'type'           => 'list',
                'allowed_values' => ['Begane grond', '1e verdieping', '2e of hoger'],
            ],
            [
                'key'            => 'buitenunit',
                'label'          => 'Locatie buitenunit',
                'type'           => 'list',
                'allowed_values' => ['Naast de woning / op de grond', 'Op het plat dak', 'Balkon of loggia'],
            ],
        ];

        foreach ($fields as $data) {
            ApiField::updateOrCreate(['key' => $data['key']], $data);
        }

        $this->command->info('Airco API fields aangemaakt: ' . count($fields));
    }

    private function seedTemplate(): void
    {
        OfferteTemplate::where('identifier', 'airco')->delete();

        $adminTeam = Team::where('is_admin', true)->first();

        $template = OfferteTemplate::create([
            'team_id'      => $adminTeam?->id,
            'naam'         => 'Airco — Standaard advies',
            'beschrijving' => 'Concept-offerte voor airco aanvragen via de website.',
            'categorie'    => 'airco',
            'identifier'   => 'airco',
        ]);

        $introTekst = "Beste [naam],

Hartelijk dank voor uw interesse in een airco. Op basis van uw antwoorden stellen wij een passend advies op.

Een airconditioner zorgt jaarrond voor een comfortabel binnenklimaat — koelen in de zomer én energiezuinig verwarmen in de winter. In deze offerte vindt u een overzicht van het geadviseerde systeem en de bijbehorende kosten.

Heeft u vragen? Neem gerust contact met ons op via 085-369 7127 of info@energx.nl.";

        $productBeschrijving = "De Mitsubishi Electric MSZ-HR serie is een van de meest betrouwbare en energiezuinige aircosystemen op de markt. Het systeem werkt als split-unit: een compacte binnenunit aan de muur en een buitenunit buiten de woning.

Dankzij de invertertechnologie past het systeem het vermogen continu aan op de werkelijke behoefte, wat resulteert in een laag energieverbruik. Het systeem is geschikt voor koelen, verwarmen en ontvochtigen.

Mitsubishi Electric biedt uitgebreide garantie en alle installaties worden uitgevoerd door gecertificeerde F-gassen installateurs.";

        $specs = [
            ['label' => 'Koelvermogen',     'waarde' => '2,5 kW (MSZ-HR25)'],
            ['label' => 'Verwarmingsvermogen', 'waarde' => '3,2 kW'],
            ['label' => 'Energielabel',     'waarde' => 'A++'],
            ['label' => 'Geluidsniveau',    'waarde' => '19 dB(A) binnen'],
            ['label' => 'Koelmiddel',       'waarde' => 'R32 (laag GWP)'],
            ['label' => 'Garantie',         'waarde' => '5 jaar'],
            ['label' => 'Bediening',        'waarde' => 'Afstandsbediening + app'],
        ];

        $acceptatieTekst = "Ga je akkoord met deze offerte? Klik op de knop hieronder om digitaal te bevestigen. Je ontvangt een bevestiging per e-mail.

Na akkoord nemen wij binnen één werkdag contact met je op om de installatie in te plannen via een gecertificeerde F-gassen installateur in jouw regio.";

        $secties = [
            [
                'type'     => 'voorblad',
                'titel'    => 'Offerte airco',
                'inhoud'   => [],
                'volgorde' => 1,
            ],
            [
                'type'     => 'introductie',
                'titel'    => 'Uw persoonlijk advies',
                'inhoud'   => ['tekst' => $introTekst],
                'volgorde' => 2,
            ],
            [
                'type'     => 'product',
                'titel'    => 'Mitsubishi Electric airco',
                'inhoud'   => [
                    'beschrijving' => $productBeschrijving,
                    'specs'        => $specs,
                ],
                'volgorde' => 3,
            ],
            [
                'type'     => 'prijzen',
                'titel'    => 'Prijsoverzicht',
                'inhoud'   => [],
                'volgorde' => 4,
            ],
            [
                'type'     => 'acceptatie',
                'titel'    => 'Akkoord geven',
                'inhoud'   => ['tekst' => $acceptatieTekst],
                'volgorde' => 5,
            ],
        ];

        foreach ($secties as $sectie) {
            OfferteTemplateSectie::create(array_merge($sectie, ['template_id' => $template->id]));
        }

        $this->command->info("Template '{$template->naam}' aangemaakt (identifier: airco).");
    }
}
