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
                'titel'        => 'Offerte zonder schouwing',
                'beschrijving' => 'Deze offerte is uitgebracht op basis van de door u aangeleverde informatie. Na akkoord wordt de installatie door de technisch specialist voorbereid. Hiervoor vragen we u mogelijk om nog enkele foto\'s aan te leveren van de groepenkast, de positie waar de laadpaal moet komen en het eventuele graafwerk.',
            ],
            [
                'titel'        => 'Vereisten meterkast',
                'beschrijving' => 'De offerte gaat er vanuit dat uw meterkast geschikt is, een hoofdschakelaar heeft en dat er voldoende ruimte is voor de groepenkastuitbreiding. Indien dit niet het geval is zijn de volgende meerkosten van toepassing: plaatsen hoofdschakelaar (€ 75,00), uitbreidingskastje — vereist 20×20 cm vrije ruimte (€ 75,00), 3-fase voorbereiding indien u een 1-fase meterkast heeft en wilt verzwaren (€ 250,00). Indien uw meterkast niet geschikt is doen wij een voorstel om de volledige meterkast te vervangen.',
            ],
            [
                'titel'        => 'Beoordeling na akkoord',
                'beschrijving' => 'Na het accepteren van de offerte wordt de meterkast aan de hand van de door u aangeleverde foto\'s door de technisch specialist beoordeeld. Indien er sprake is van meerwerk wordt dit aan u voorgelegd. Indien u niet akkoord gaat met de meerkosten kunt u op dat moment nog kosteloos annuleren.',
            ],
            [
                'titel'        => 'Planningsvoorstel & bindendheid',
                'beschrijving' => 'De offerte is pas bindend nadat wij uw situatie aan de hand van foto\'s hebben beoordeeld en u een planningsvoorstel voor installatie sturen.',
            ],
            [
                'titel'        => 'Meerkosten & tarieven',
                'beschrijving' => 'Indien de situatie afwijkt van de door u aangeleverde informatie kan dit tot meerkosten leiden. Voor extra kabel geldt een toeslag van € 10,00 per meter. Voor graafwerkzaamheden bedraagt dit € 35,00 per meter. Indien ter plekke blijkt dat er in de meterkast niet voldoende ruimte is voor de groepsuitbreiding dient een uitbreidingskastje geplaatst te worden (€ 75,00). De genoemde tarieven zijn exclusief btw.',
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
