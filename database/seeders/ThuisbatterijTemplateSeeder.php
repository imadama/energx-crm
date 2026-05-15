<?php

namespace Database\Seeders;

use App\Models\ApiField;
use App\Models\OfferteTemplate;
use App\Models\OfferteTemplateSectie;
use Illuminate\Database\Seeder;

class ThuisbatterijTemplateSeeder extends Seeder
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
                'key'            => 'zonnepanelen',
                'label'          => 'Zonnepanelen aanwezig',
                'type'           => 'list',
                'allowed_values' => ['ja', 'gepland', 'nee'],
            ],
            [
                'key'            => 'doelen',
                'label'          => 'Doelen thuisbatterij',
                'type'           => 'list_multiple',
                'allowed_values' => ['opslaan', 'besparen', 'backup', 'onafhankelijk'],
            ],
            [
                'key'            => 'verbruik',
                'label'          => 'Jaarlijks stroomverbruik',
                'type'           => 'list',
                'allowed_values' => ['klein', 'gemiddeld', 'groot'],
            ],
            [
                'key'            => 'capaciteit',
                'label'          => 'Gewenste opslagcapaciteit',
                'type'           => 'list',
                'allowed_values' => ['klein', 'gemiddeld', 'groot', 'weet-niet'],
            ],
        ];

        foreach ($fields as $data) {
            ApiField::updateOrCreate(['key' => $data['key']], $data);
        }

        $this->command->info('Thuisbatterij API fields aangemaakt: ' . count($fields));
    }

    private function seedTemplate(): void
    {
        OfferteTemplate::where('identifier', 'thuisbatterij')->delete();

        $template = OfferteTemplate::create([
            'naam'        => 'Thuisbatterij — Standaard advies',
            'beschrijving' => 'Concept-offerte voor thuisbatterij aanvragen via de website.',
            'categorie'   => 'thuisbatterij',
            'identifier'  => 'thuisbatterij',
        ]);

        $introTekst = "Beste [naam],

Hartelijk dank voor uw interesse in een thuisbatterij. Op basis van uw antwoorden stellen wij een passend advies op.

Een thuisbatterij stelt u in staat om de door uw zonnepanelen opgewekte energie op te slaan en op elk moment te gebruiken — ook 's avonds of bij stroomuitval. In deze offerte vindt u een overzicht van het geadviseerde systeem en de bijbehorende kosten.

Heeft u vragen? Neem gerust contact met ons op via 085-369 7127 of info@energx.nl.";

        $productBeschrijving = "De Alpha ESS SMILE5 en SMILE-B3 Plus zijn bewezen thuisbatterij-systemen die al bij duizenden Nederlandse huishoudens geïnstalleerd zijn. Het systeem is modulair opgebouwd: elke module levert 5,7 kWh aan opslagcapaciteit, uitbreidbaar naar 11,4 kWh of 17,1 kWh.

Het systeem werkt samen met uw bestaande omvormer of als onderdeel van een volledig nieuw systeem. Dankzij de EMS-software optimaliseert de batterij automatisch het zelfverbruik en reageert hij op dynamische energietarieven.

Alpha ESS biedt 10 jaar garantie op de batterijcellen en voldoet aan de Europese veiligheids- en kwaliteitsnormen.";

        $specs = [
            ['label' => 'Opslagcapaciteit',    'waarde' => '5,7 / 11,4 / 17,1 kWh'],
            ['label' => 'Piekvermogen',         'waarde' => '5 kW'],
            ['label' => 'Efficiëntie',          'waarde' => '> 90%'],
            ['label' => 'Garantie cellen',      'waarde' => '10 jaar'],
            ['label' => 'Batterijtype',         'waarde' => 'LFP (lithium-ijzerfosfaat)'],
            ['label' => 'Certificeringen',      'waarde' => 'IEC 62619, CE, UN 38.3'],
            ['label' => 'Installatie',          'waarde' => 'Binnen, wand- of vloermontage'],
        ];

        $acceptatieTekst = "Ga je akkoord met deze offerte? Klik op de knop hieronder om digitaal te bevestigen. Je ontvangt een bevestiging per e-mail.

Na akkoord nemen wij binnen één werkdag contact met je op om de installatie in te plannen via een gecertificeerde installateur in jouw regio.";

        $secties = [
            [
                'type'     => 'voorblad',
                'titel'    => 'Offerte thuisbatterij',
                'inhoud'   => [],
                'volgorde' => 1,
            ],
            [
                'type'     => 'intro',
                'titel'    => 'Uw persoonlijk advies',
                'inhoud'   => ['tekst' => $introTekst],
                'volgorde' => 2,
            ],
            [
                'type'     => 'product',
                'titel'    => 'Alpha ESS thuisbatterij',
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

        $this->command->info("Template '{$template->naam}' aangemaakt (identifier: thuisbatterij).");
    }
}
