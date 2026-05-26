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
        $adminTeam = Team::where('is_admin', true)->first();

        $template = OfferteTemplate::firstOrCreate(
            ['identifier' => 'airco'],
            [
                'team_id'      => $adminTeam?->id,
                'naam'         => 'Airco — Standaard advies',
                'beschrijving' => 'Concept-offerte voor airco aanvragen via de website.',
                'categorie'    => 'airco',
            ]
        );

        // Altijd secties bijwerken zodat content-wijzigingen live gaan bij elke deploy
        $template->secties()->delete();

        $introTekst = "Beste [naam],

Hartelijk dank voor uw interesse in een airco. Op basis van uw antwoorden stellen wij een passende offerte op.

Wij vertrouwen erop u hiermee een passende aanbieding te doen en zien uw reactie met belangstelling tegemoet.

Heeft u vragen? Neem gerust contact met ons op via 085-369 7127 of info@energx.nl.";

        $productBeschrijving = "De Mitsubishi Electric MSZ-HR serie is een van de meest betrouwbare en energiezuinige aircosystemen op de markt. Het systeem werkt als split-unit: een compacte binnenunit aan de muur en een buitenunit buiten de woning.

Dankzij de invertertechnologie past het systeem het vermogen continu aan op de werkelijke behoefte, wat resulteert in een laag energieverbruik. Het systeem is geschikt voor koelen, verwarmen en ontvochtigen.

Alle installaties worden uitgevoerd door gecertificeerde F-gassen installateurs conform KIWA installatievoorschrift, BRL100 en BRL200.";

        $specs = [
            ['label' => 'Koelvermogen',           'waarde' => '2,5 kW (MSZ-HR25)'],
            ['label' => 'Verwarmingsvermogen',     'waarde' => '3,2 kW'],
            ['label' => 'Energielabel',            'waarde' => 'A++'],
            ['label' => 'Geluidsniveau',           'waarde' => '19 dB(A) binnen'],
            ['label' => 'Koelmiddel',              'waarde' => 'R32 (laag GWP)'],
            ['label' => 'Koudemiddelleiding incl.','waarde' => 'Tot 5 meter'],
            ['label' => 'Certificering',           'waarde' => 'KIWA, BRL100, BRL200'],
            ['label' => 'Garantie',                'waarde' => '5 jaar'],
        ];

        $inbegrepen = [
            'Binnen- en buitendeel van de airconditioning',
            'Plaatsing condensafvoer binnenunit naar buiten op natuurlijk afschot',
            'Plaatsing muurbeugel of rubberen consoles t.b.v. het buitendeel',
            'Éénmaal muurdoorvoer d.m.v. diamantboor (diameter 60 mm)',
            'Leidinggoot ter afwerking van de koelleidingen aan de gevel',
            'Plaatsing werkschakelaar',
            'Koudemiddelleiding tot 5 meter (binnen naar buitenunit)',
            'Uitgebreide uitleg omtrent de werking van de airconditioning',
            'Installatie conform KIWA installatievoorschrift, BRL100 & BRL200',
        ];

        $meerkosten = [
            'Dakdoorvoer(en) t.b.v. koelleidingen',
            'Fysieke schouw & advies op locatie i.v.m. technische haalbaarheid — € 60,- incl. btw',
            'Reiskostenvergoeding > 15 km vanaf eigen adres — € 65,- incl. btw',
            'Leidingen onder dakpannen, kruipruimten of knieschotten — € 115,- incl. btw',
            'Extra ballast om resonantie te voorkomen op daken',
            'Koudemiddelleiding > 5 m — € 65,- incl. btw per extra meter',
            'Plaatsing extra groep in de meterkast inclusief bekabeling',
            'Betonboringen of boringen vanaf twee met de diamantboor',
            'Leveren en monteren van condenspomp(en) t.b.v. condenswater',
            'Hoogwerker of steiger(s)',
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
                    'inbegrepen'   => $inbegrepen,
                    'meerkosten'   => $meerkosten,
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

        $this->command->info("Template '{$template->naam}' bijgewerkt (identifier: airco).");
    }
}
