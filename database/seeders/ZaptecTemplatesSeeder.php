<?php

namespace Database\Seeders;

use App\Models\OfferteTemplate;
use App\Models\OfferteTemplateSectie;
use App\Models\OfferteTemplateRegel;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ZaptecTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $zaptec       = Product::where('naam', 'like', '%Zaptec Go%')->firstOrFail();
        $installaties = Product::where('categorie', 'installatie')->orderBy('naam')->get();

        $introTekst = "Beste [naam],

Hartelijk dank voor uw interesse in een laadpaal voor thuis. Wij zijn blij u een passend voorstel te kunnen doen voor de installatie van een Zaptec Go laadpaal.

De Zaptec Go is één van de meest gewaardeerde thuisladers van dit moment — compact, slim en ontworpen voor dagelijks gebruik. Met de bijbehorende app beheert u alles op afstand: laadschema's instellen, verbruik inzien en profiteren van de laagste energietarieven via de Eco-modus.

In deze offerte vindt u een overzicht van de laadpaal, de installatiekosten en een beschrijving van onze werkwijze. Heeft u vragen? Neem gerust contact met ons op.";

        $productBeschrijving = "De Zaptec Go is een bekroonde thuislader die tot 22 kW laadvermogen levert en compatibel is met alle elektrische voertuigen. Met een gewicht van slechts 1,3 kg en een formaat kleiner dan een iPad past hij op elke locatie.

Dankzij ingebouwde beveiliging (temperatuur-, overbelasting-, reststroom- en vochtigheidsbewaking) is de lader veilig in alle weersomstandigheden. Via wifi of 4G LTE-M blijft het apparaat altijd verbonden voor over-the-air updates en app-beheer.

De Zaptec Go beschikt over een 5 jaar garantie en is winnaar van de Red Dot Design Award.";

        $specs = [
            ['label' => 'Max. laadvermogen',  'waarde' => '22 kW'],
            ['label' => 'Gewicht',             'waarde' => '1,3 kg'],
            ['label' => 'Connectiviteit',      'waarde' => 'WiFi / 4G LTE-M'],
            ['label' => 'Garantie',            'waarde' => '5 jaar'],
            ['label' => 'Normen',              'waarde' => 'IEC 61851-1 / IEC 60364'],
            ['label' => 'Kleuropties',         'waarde' => '6 kleuren beschikbaar'],
            ['label' => 'Award',               'waarde' => 'Red Dot Design Award'],
            ['label' => 'App',                 'waarde' => 'Zaptec App (iOS & Android)'],
        ];

        $werkwijzeStappen = [
            [
                'titel'       => 'Aanvraag & adviesgesprek',
                'beschrijving' => 'Na ontvangst van uw offerteverzoek neemt een van onze adviseurs contact met u op. We bespreken uw situatie, de meterkastopstelling en de gewenste locatie van de laadpaal.',
            ],
            [
                'titel'       => 'Inmeten & planning',
                'beschrijving' => 'Een gecertificeerde installateur komt langs om de situatie te beoordelen en de installatie in te plannen. We controleren de groepenkast en kabeltracé.',
            ],
            [
                'titel'       => 'Installatie',
                'beschrijving' => 'De installateur installeert de Zaptec Go conform de NEN 1010 norm en de eisen van de netbeheerder. De laadpaal wordt aangesloten, getest en ingesteld.',
            ],
            [
                'titel'       => 'Activatie & uitleg',
                'beschrijving' => 'We koppelen de Zaptec App aan uw account en leggen uit hoe u laadschema\'s instelt, het verbruik bijhoudt en de Eco-modus gebruikt.',
            ],
            [
                'titel'       => 'Nazorg',
                'beschrijving' => 'Na de installatie staan wij voor u klaar voor vragen of ondersteuning. Als lokale installateur zijn we snel bereikbaar en kennen we uw specifieke situatie.',
            ],
        ];

        $acceptatieTekst = "Ga je akkoord met deze offerte? Klik op de knop hieronder om digitaal te bevestigen. Je ontvangt een bevestiging per e-mail.

Na akkoord nemen wij binnen één werkdag contact met u op om de installatie in te plannen.";

        foreach ($installaties as $installatie) {
            // Bepaal naam op basis van installatietype
            $isKruipruimte = str_contains(strtolower($installatie->naam), 'kruipruimte');
            $templateNaam  = 'Zaptec Go — ' . ($isKruipruimte ? 'Installatie via kruipruimte' : 'Standaard installatie');
            $templateBeschr = $isKruipruimte
                ? 'Zaptec Go 22 kW laadpaal met installatie via kruipruimte en gevelbevestiging.'
                : 'Zaptec Go 22 kW laadpaal met standaard installatie direct achter de meterkast.';

            // Verwijder eventueel bestaande template met zelfde naam
            OfferteTemplate::where('naam', $templateNaam)->delete();

            $template = OfferteTemplate::create([
                'naam'        => $templateNaam,
                'beschrijving'=> $templateBeschr,
                'categorie'   => 'laadpaal',
            ]);

            // Secties
            $secties = [
                [
                    'type'     => 'voorblad',
                    'titel'    => 'Voorblad',
                    'inhoud'   => [],
                    'volgorde' => 0,
                ],
                [
                    'type'     => 'introductie',
                    'titel'    => 'Introductie',
                    'inhoud'   => ['tekst' => $introTekst],
                    'volgorde' => 1,
                ],
                [
                    'type'     => 'prijzen',
                    'titel'    => 'Prijsoverzicht',
                    'inhoud'   => [],
                    'volgorde' => 2,
                ],
                [
                    'type'     => 'product',
                    'titel'    => 'Zaptec Go — productinformatie',
                    'inhoud'   => [
                        'beschrijving' => $productBeschrijving,
                        'specs'        => $specs,
                    ],
                    'volgorde' => 3,
                ],
                [
                    'type'     => 'werkwijze',
                    'titel'    => 'Onze werkwijze',
                    'inhoud'   => ['stappen' => $werkwijzeStappen],
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
                $template->secties()->create($sectie);
            }

            // Standaard prijsregels
            $template->regels()->create([
                'product_id'    => $zaptec->id,
                'naam'          => $zaptec->naam,
                'beschrijving'  => 'Inclusief montagebeugel en 5 jaar garantie',
                'aantal'        => 1,
                'eenheidsprijs' => $zaptec->prijs,
                'volgorde'      => 0,
            ]);

            $template->regels()->create([
                'product_id'    => $installatie->id,
                'naam'          => $installatie->naam,
                'beschrijving'  => 'Inclusief aardlekautomaat en bekabeling',
                'aantal'        => 1,
                'eenheidsprijs' => $installatie->prijs,
                'volgorde'      => 1,
            ]);

            $this->command->info("Template aangemaakt: {$templateNaam}");
        }
    }
}
