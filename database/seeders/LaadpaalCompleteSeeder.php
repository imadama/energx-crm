<?php

namespace Database\Seeders;

use App\Models\ApiField;
use App\Models\OfferteTemplate;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LaadpaalCompleteSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedApiFields();
        $this->seedProducts();
        $this->seedTemplates();
    }

    // ── API Fields ─────────────────────────────────────────────────────────────

    private function seedApiFields(): void
    {
        $fields = [
            [
                'key'            => 'situatie',
                'label'          => 'Installatiesituatie',
                'type'           => 'list',
                'allowed_values' => ['meterkast', 'kruipruimte', 'graafwerk'],
            ],
            [
                'key'            => 'model',
                'label'          => 'Laadpaal model',
                'type'           => 'list',
                'allowed_values' => ['zaptec-go', 'zaptec-go2', 'ratio-io6pro', 'ratio-io6', 'ratio-solar', 'alfen-sline'],
            ],
            [
                'key'            => 'meters_kabel',
                'label'          => 'Meters voedingskabel',
                'type'           => 'integer',
                'allowed_values' => null,
            ],
            [
                'key'            => 'graawerk_meters',
                'label'          => 'Meters graafwerk',
                'type'           => 'integer',
                'allowed_values' => null,
            ],
        ];

        foreach ($fields as $data) {
            ApiField::updateOrCreate(['key' => $data['key']], $data);
        }

        $this->command->info('API fields: ' . ApiField::count() . ' velden.');
    }

    // ── Producten ──────────────────────────────────────────────────────────────

    private function seedProducts(): void
    {
        // ── Laadpalen ─────────────────────────────────────────

        $laadpalen = [
            [
                'naam'         => 'Zaptec Go - 22 kW',
                'beschrijving' => null,
                'prijs'        => 599.00,
                'merk'         => 'Zaptec',
                'order'        => 10,
                'model_key'    => 'zaptec-go',
            ],
            [
                'naam'         => 'Zaptec Go 2',
                'beschrijving' => null,
                'prijs'        => 749.00,
                'merk'         => 'Zaptec',
                'order'        => 11,
                'model_key'    => 'zaptec-go2',
            ],
            [
                'naam'         => 'Ratio iO6 Pro',
                'beschrijving' => null,
                'prijs'        => 649.00,
                'merk'         => 'Ratio',
                'order'        => 20,
                'model_key'    => 'ratio-io6pro',
            ],
            [
                'naam'         => 'Ratio iO6',
                'beschrijving' => null,
                'prijs'        => 549.00,
                'merk'         => 'Ratio',
                'order'        => 21,
                'model_key'    => 'ratio-io6',
            ],
            [
                'naam'         => 'Ratio Solar',
                'beschrijving' => null,
                'prijs'        => 399.00,
                'merk'         => 'Ratio',
                'order'        => 22,
                'model_key'    => 'ratio-solar',
            ],
            [
                'naam'         => 'Alfen Eve S-line',
                'beschrijving' => null,
                'prijs'        => 799.00,
                'merk'         => 'Alfen',
                'order'        => 30,
                'model_key'    => 'alfen-sline',
            ],
        ];

        foreach ($laadpalen as $data) {
            $modelKey = $data['model_key'];
            unset($data['model_key']);

            Product::updateOrCreate(
                ['naam' => $data['naam']],
                array_merge($data, [
                    'categorie'             => 'laadpaal',
                    'actief'                => true,
                    'generator_mode'        => 'conditional',
                    'generator_conditions'  => [
                        ['and' => [['field' => 'model', 'op' => 'eq', 'value' => $modelKey]]],
                    ],
                    'generator_value_rules' => null,
                ])
            );
        }

        // ── Installaties ──────────────────────────────────────

        Product::updateOrCreate(
            ['naam' => 'Standaard installatie (direct achter de meterkast)'],
            [
                'beschrijving'          => "- Wandmontage\n- Aardlekautomaat incl plaatsing\n- Tot 10m Voedings- en signaalkabel",
                'prijs'                 => 399.00,
                'categorie'             => 'installatie',
                'merk'                  => null,
                'actief'                => true,
                'order'                 => 50,
                'generator_mode'        => 'conditional',
                'generator_conditions'  => [
                    ['and' => [['field' => 'situatie', 'op' => 'eq', 'value' => 'meterkast']]],
                ],
                'generator_value_rules' => null,
            ]
        );

        Product::updateOrCreate(
            ['naam' => 'Standaard installatie (kruipruimte)'],
            [
                'beschrijving'          => "- Wandmontage\n- Aardlekautomaat incl plaatsing\n- Tot 10m Voedings- en signaalkabel\n- Kabel via kruipruimte, incl 1m graaf/straatwerk",
                'prijs'                 => 489.00,
                'categorie'             => 'installatie',
                'merk'                  => null,
                'actief'                => true,
                'order'                 => 51,
                'generator_mode'        => 'conditional',
                'generator_conditions'  => [
                    ['and' => [['field' => 'situatie', 'op' => 'eq', 'value' => 'kruipruimte']]],
                ],
                'generator_value_rules' => null,
            ]
        );

        Product::updateOrCreate(
            ['naam' => 'Standaard installatie (kruipruimte + graafwerk)'],
            [
                'beschrijving'          => "- Wandmontage\n- Aardlekautomaat incl plaatsing\n- Tot 10m Voedings- en signaalkabel\n- Kabel via kruipruimte\n- Incl 5m graaf/straatwerk",
                'prijs'                 => 699.00,
                'categorie'             => 'installatie',
                'merk'                  => null,
                'actief'                => true,
                'order'                 => 52,
                'generator_mode'        => 'conditional',
                'generator_conditions'  => [
                    ['and' => [['field' => 'situatie', 'op' => 'eq', 'value' => 'graafwerk']]],
                ],
                'generator_value_rules' => null,
            ]
        );

        Product::updateOrCreate(
            ['naam' => 'Extra meter voedingskabel'],
            [
                'beschrijving'          => '€ 10,00 per meter boven de eerste 10 meter',
                'prijs'                 => 10.00,
                'categorie'             => 'installatie',
                'merk'                  => null,
                'actief'                => true,
                'order'                 => 53,
                'generator_mode'        => 'conditional',
                'generator_conditions'  => [
                    ['and' => [['field' => 'meters_kabel', 'op' => 'gt', 'value' => 10]]],
                ],
                'generator_value_rules' => [
                    'aantal' => ['enabled' => true, 'field' => 'meters_kabel', 'op' => '-', 'delta' => 10],
                    'prijs'  => ['enabled' => false],
                ],
            ]
        );

        Product::updateOrCreate(
            ['naam' => 'Extra meter graafwerk'],
            [
                'beschrijving'          => '€ 35,00 per meter graafwerkzaamheden',
                'prijs'                 => 35.00,
                'categorie'             => 'installatie',
                'merk'                  => null,
                'actief'                => true,
                'order'                 => 54,
                'generator_mode'        => 'conditional',
                'generator_conditions'  => [
                    ['and' => [['field' => 'graawerk_meters', 'op' => 'gt', 'value' => 5]]],
                ],
                'generator_value_rules' => [
                    'aantal' => ['enabled' => true, 'field' => 'graawerk_meters', 'op' => '-', 'delta' => 5],
                    'prijs'  => ['enabled' => false],
                ],
            ]
        );

        $this->command->info('Producten: ' . Product::count() . ' stuks.');
    }

    // ── Offerte templates ──────────────────────────────────────────────────────

    private function seedTemplates(): void
    {
        $werkwijzeStappen = $this->werkwijzeStappen();
        $acceptatieTekst  = $this->acceptatieTekst();

        $brands = $this->brandDefinitions();

        foreach ($brands as $brand) {
            foreach (['direct', 'kruip'] as $situatie) {
                $identifier = $brand['id'] . '-' . $situatie;
                $isKruip    = $situatie === 'kruip';

                $naam = $brand['naam'] . ' — ' . ($isKruip ? 'Installatie via kruipruimte' : 'Standaard installatie');
                $beschr = $brand['naam'] . ' laadpaal met ' . ($isKruip ? 'installatie via kruipruimte.' : 'standaard installatie direct achter de meterkast.');

                $intro = $this->buildIntro($brand, $isKruip);

                // Update identifier op bestaande template met deze naam, of maak nieuw aan
                $existing = OfferteTemplate::where('identifier', $identifier)
                    ->orWhere('naam', $naam)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'naam'         => $naam,
                        'beschrijving' => $beschr,
                        'categorie'    => 'laadpaal',
                        'identifier'   => $identifier,
                    ]);
                    $existing->secties()->delete();
                    $existing->regels()->delete();
                    $template = $existing;
                } else {
                    $template = OfferteTemplate::create([
                        'naam'         => $naam,
                        'beschrijving' => $beschr,
                        'categorie'    => 'laadpaal',
                        'identifier'   => $identifier,
                    ]);
                }

                // Secties
                $secties = [
                    ['type' => 'voorblad',    'titel' => 'Voorblad',              'inhoud' => [],                                                          'volgorde' => 0],
                    ['type' => 'introductie', 'titel' => 'Introductie',           'inhoud' => ['tekst' => $intro],                                         'volgorde' => 1],
                    ['type' => 'prijzen',     'titel' => 'Prijsoverzicht',        'inhoud' => [],                                                          'volgorde' => 2],
                    ['type' => 'product',     'titel' => $brand['naam'] . ' — productinformatie', 'inhoud' => ['beschrijving' => $brand['beschrijving'], 'specs' => $brand['specs']], 'volgorde' => 3],
                    ['type' => 'werkwijze',   'titel' => 'Onze werkwijze',        'inhoud' => ['stappen' => $werkwijzeStappen],                            'volgorde' => 4],
                    ['type' => 'acceptatie',  'titel' => 'Akkoord geven',         'inhoud' => ['tekst' => $acceptatieTekst],                               'volgorde' => 5],
                ];

                foreach ($secties as $sectie) {
                    $template->secties()->create($sectie);
                }

                $this->command->info("Template: {$identifier}");
            }
        }

        // Verwijder verouderde Zaptec templates met oude identifiers (zaptec-go, zaptec-go-kruip)
        // zodat de nieuwe go-direct / go-kruip de enige zijn
        OfferteTemplate::whereIn('identifier', ['zaptec-go', 'zaptec-go-kruip'])->delete();

        $this->command->info('Templates totaal: ' . OfferteTemplate::count() . ' stuks.');
    }

    // ── Brand definitie ────────────────────────────────────────────────────────

    /** @return array<int,array<string,mixed>> */
    private function brandDefinitions(): array
    {
        return [
            [
                'id'   => 'go',
                'naam' => 'Zaptec Go - 22 kW',
                'beschrijving' => "De Zaptec Go is een bekroonde thuislader die tot 22 kW laadvermogen levert en compatibel is met alle elektrische voertuigen. Met een gewicht van slechts 1,3 kg en een formaat kleiner dan een iPad past hij op elke locatie.\n\nDankzij ingebouwde beveiliging (temperatuur-, overbelasting-, reststroom- en vochtigheidsbewaking) is de lader veilig in alle weersomstandigheden. Via wifi of 4G LTE-M blijft het apparaat altijd verbonden voor over-the-air updates en app-beheer.\n\nDe Zaptec Go beschikt over een 5 jaar garantie en is winnaar van de Red Dot Design Award.",
                'specs' => [
                    ['label' => 'Max. laadvermogen', 'waarde' => '22 kW'],
                    ['label' => 'Gewicht',            'waarde' => '1,3 kg'],
                    ['label' => 'Connectiviteit',     'waarde' => 'WiFi / 4G LTE-M'],
                    ['label' => 'Garantie',           'waarde' => '5 jaar'],
                    ['label' => 'Normen',             'waarde' => 'IEC 61851-1 / IEC 60364'],
                    ['label' => 'Kleuropties',        'waarde' => '6 kleuren beschikbaar'],
                    ['label' => 'Award',              'waarde' => 'Red Dot Design Award'],
                    ['label' => 'App',                'waarde' => 'Zaptec App (iOS & Android)'],
                ],
                'intro_product' => 'Zaptec Go',
                'intro_extra'   => 'De Zaptec Go is één van de meest gewaardeerde thuisladers van dit moment — compact, slim en ontworpen voor dagelijks gebruik. Met de bijbehorende app beheert u alles op afstand: laadschema\'s instellen, verbruik inzien en profiteren van de laagste energietarieven via de Eco-modus.',
            ],
            [
                'id'   => 'go2',
                'naam' => 'Zaptec Go 2',
                'beschrijving' => "De Zaptec Go 2 is de nieuwste generatie thuislader van Zaptec en de eerste in zijn klasse met ondersteuning voor Vehicle-to-Grid (V2G). Hiermee laadt u niet alleen uw auto op, maar kunt u ook energie terugleveren aan het net of uw eigen huis — maximaal rendement op uw zonnepanelen.\n\nNet als zijn voorganger is de Go 2 compact, licht en volledig app-gestuurd. De ingebouwde 4G LTE-M en WiFi-verbinding zorgen voor automatische updates en naadloze integratie met energie-managementsystemen.\n\nDe Zaptec Go 2 heeft een 5 jaar garantie en is geschikt voor zowel 1- als 3-fase aansluiting.",
                'specs' => [
                    ['label' => 'Max. laadvermogen', 'waarde' => '22 kW'],
                    ['label' => 'V2G / V2H',         'waarde' => 'Ja — bidirectioneel laden'],
                    ['label' => 'Connectiviteit',    'waarde' => 'WiFi / 4G LTE-M'],
                    ['label' => 'Garantie',          'waarde' => '5 jaar'],
                    ['label' => 'Fases',             'waarde' => '1-fase en 3-fase'],
                    ['label' => 'Normen',            'waarde' => 'IEC 61851-1 / IEC 60364'],
                    ['label' => 'App',               'waarde' => 'Zaptec App (iOS & Android)'],
                ],
                'intro_product' => 'Zaptec Go 2',
                'intro_extra'   => 'De Zaptec Go 2 is de toekomstbestendige keuze voor thuis: dankzij V2G-technologie kunt u uw elektrische auto inzetten als thuisbatterij en energie terugleveren wanneer de stroomprijzen hoog zijn.',
            ],
            [
                'id'   => 'io6pro',
                'naam' => 'Ratio iO6 Pro',
                'beschrijving' => "De Ratio iO6 Pro is een slimme thuislader met ingebouwde laadpas-ondersteuning (RFID) en uitgebreide integratiemogelijkheden voor thuisbatterijen en zonnepanelen. Dankzij de dynamische laadregeling maximaliseert u het gebruik van eigen zonne-energie.\n\nDe iO6 Pro is beschikbaar als 1-fase (7,4 kW) of 3-fase (22 kW) uitvoering en ondersteunt Modbus TCP voor directe koppeling met omvormers. De laadpas-functie maakt afrekening van zakelijk gebruik eenvoudig.\n\nInclusive Ratio Connect app voor realtime inzicht in laadverbruik en slimme laadschema's.",
                'specs' => [
                    ['label' => 'Max. laadvermogen', 'waarde' => '22 kW (3-fase)'],
                    ['label' => 'Laadpas (RFID)',    'waarde' => 'Ja'],
                    ['label' => 'Zonnestroom',       'waarde' => 'Dynamisch laden'],
                    ['label' => 'Connectiviteit',    'waarde' => 'WiFi / Ethernet'],
                    ['label' => 'Garantie',          'waarde' => '3 jaar'],
                    ['label' => 'Normen',            'waarde' => 'IEC 61851-1 / IEC 60364'],
                    ['label' => 'App',               'waarde' => 'Ratio Connect App'],
                ],
                'intro_product' => 'Ratio iO6 Pro',
                'intro_extra'   => 'De Ratio iO6 Pro is de slimste keuze voor wie zonnepanelen heeft en maximaal wil profiteren van eigen opgewekte energie. De dynamische laadregeling zorgt ervoor dat u altijd zo goedkoop mogelijk laadt.',
            ],
            [
                'id'   => 'io6',
                'naam' => 'Ratio iO6',
                'beschrijving' => "De Ratio iO6 is een betrouwbare thuislader met ingebouwde laadpas-ondersteuning en zonnestroom-optimalisatie. De lader is eenvoudig te installeren en biedt alle functies die u nodig heeft voor slim en veilig laden thuis.\n\nDe iO6 is beschikbaar als 1-fase (7,4 kW) of 3-fase (22 kW) versie en kan worden uitgebreid met de Ratio Connect app voor inzicht in laadverbruik en het instellen van slimme laadschema's.\n\nDe iO6 is CE-gecertificeerd en voldoet aan alle Nederlandse veiligheidsvoorschriften.",
                'specs' => [
                    ['label' => 'Max. laadvermogen', 'waarde' => '22 kW (3-fase)'],
                    ['label' => 'Laadpas (RFID)',    'waarde' => 'Ja'],
                    ['label' => 'Zonnestroom',       'waarde' => 'Ja'],
                    ['label' => 'Connectiviteit',    'waarde' => 'WiFi'],
                    ['label' => 'Garantie',          'waarde' => '3 jaar'],
                    ['label' => 'Normen',            'waarde' => 'IEC 61851-1 / IEC 60364'],
                    ['label' => 'App',               'waarde' => 'Ratio Connect App'],
                ],
                'intro_product' => 'Ratio iO6',
                'intro_extra'   => 'De Ratio iO6 biedt een uitstekende prijs-kwaliteitverhouding voor wie slim en betrouwbaar wil laden — inclusief laadpas-functie voor eenvoudig gebruik.',
            ],
            [
                'id'   => 'solar',
                'naam' => 'Ratio Solar',
                'beschrijving' => "De Ratio Solar is speciaal ontwikkeld voor laden op zonne-energie. De lader detecteert automatisch wanneer uw panelen meer energie opwekken dan uw huis verbruikt en gebruikt dat overschot om uw auto op te laden — volledig automatisch en zonder extra instellingen.\n\nDe Solar is een eenvoudige, compacte lader zonder overbodige functies. Perfect voor wie duurzaam wil laden zonder complexe installatie.\n\nGeleverd met wandmontagebeugel en 5 meter kabel.",
                'specs' => [
                    ['label' => 'Max. laadvermogen', 'waarde' => '11 kW (1-fase)'],
                    ['label' => 'Zonnestroom',       'waarde' => 'Automatische zonnestroom-prioriteit'],
                    ['label' => 'Laadpas',           'waarde' => 'Nee'],
                    ['label' => 'Connectiviteit',    'waarde' => 'WiFi'],
                    ['label' => 'Garantie',          'waarde' => '2 jaar'],
                    ['label' => 'Normen',            'waarde' => 'IEC 61851-1 / IEC 60364'],
                    ['label' => 'App',               'waarde' => 'Ratio Connect App'],
                ],
                'intro_product' => 'Ratio Solar',
                'intro_extra'   => 'De Ratio Solar is de eenvoudigste manier om uw auto op te laden met eigen zonnestroom. Zonder ingewikkelde instellingen — de lader regelt het automatisch.',
            ],
            [
                'id'   => 'sline',
                'naam' => 'Alfen Eve S-line',
                'beschrijving' => "De Alfen Eve S-line is een professionele thuislader met ingebouwde MID-gecertificeerde meter voor nauwkeurige energiemeting. Ideaal voor wie zakelijk rijdt en de laadkosten wil declareren of doorbelasten.\n\nDe Eve S-line is uitgerust met een RFID-kaartlezer voor toegangscontrole en ondersteunt meerdere laadpassen en -netwerken. De lader is geschikt voor 1-fase (7,4 kW) en 3-fase (22 kW) aansluiting.\n\nAlfen is een Nederlandse fabrikant met meer dan 85 jaar ervaring in elektrische installaties.",
                'specs' => [
                    ['label' => 'Max. laadvermogen', 'waarde' => '22 kW (3-fase)'],
                    ['label' => 'MID-meter',         'waarde' => 'Ja — fiscaal afrekenbaar'],
                    ['label' => 'Laadpas (RFID)',    'waarde' => 'Ja'],
                    ['label' => 'Connectiviteit',    'waarde' => 'WiFi / Ethernet'],
                    ['label' => 'Garantie',          'waarde' => '3 jaar'],
                    ['label' => 'Normen',            'waarde' => 'IEC 61851-1 / IEC 60364'],
                    ['label' => 'Fabrikant',         'waarde' => 'Alfen — Nederlands merk'],
                ],
                'intro_product' => 'Alfen Eve S-line',
                'intro_extra'   => 'De Alfen Eve S-line is de ideale keuze voor zakelijke rijders: de ingebouwde MID-meter registreert elk kWh nauwkeurig zodat u laadkosten eenvoudig kunt declareren.',
            ],
        ];
    }

    private function buildIntro(array $brand, bool $isKruip): string
    {
        $installatieZin = $isKruip
            ? 'De installatie wordt uitgevoerd via de kruipruimte, inclusief het nodige gevel- en eventueel straatwerk.'
            : 'De installatie wordt direct achter de meterkast uitgevoerd — de meest efficiënte en voordelige oplossing.';

        return "Beste [naam],

Hartelijk dank voor uw interesse in een laadpaal voor thuis. Wij zijn blij u een passend voorstel te kunnen doen voor de installatie van een {$brand['intro_product']}.

{$brand['intro_extra']}

{$installatieZin}

In deze offerte vindt u een overzicht van de laadpaal, de installatiekosten en een beschrijving van onze werkwijze. Heeft u vragen? Neem gerust contact met ons op.";
    }

    private function werkwijzeStappen(): array
    {
        return [
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
    }

    private function acceptatieTekst(): string
    {
        return "Ga je akkoord met deze offerte? Klik op de knop hieronder om digitaal te bevestigen. Je ontvangt een bevestiging per e-mail.

Na akkoord nemen wij binnen één werkdag contact met u op om de installatie in te plannen.";
    }
}
