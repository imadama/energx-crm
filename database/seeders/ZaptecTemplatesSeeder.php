<?php

namespace Database\Seeders;

use App\Models\OfferteTemplate;
use App\Models\Product;
use App\Services\DocumentRenderer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ZaptecTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $introHtml = '<h1>Offerte laadpaal installatie</h1><p>Beste {{ klant.naam }},</p><p>Hartelijk dank voor uw interesse in een laadpaal voor thuis. Wij zijn blij u een passend voorstel te doen voor de installatie van een Zaptec Go laadpaal.</p><p>De Zaptec Go is één van de meest gewaardeerde thuisladers van dit moment — compact, slim en ontworpen voor dagelijks gebruik. In deze offerte vindt u een overzicht van de laadpaal, de installatiekosten en een beschrijving van onze werkwijze.</p>';

        $standaardDoc = $this->maakDocument([
            ['type' => 'tekst', 'inhoud' => ['html' => $introHtml]],
            ['type' => 'prijstabel', 'inhoud' => []],
            ['type' => 'tekst', 'inhoud' => ['html' => '<h2>Werkwijze</h2><p>Na uw akkoord wordt de installatie door onze specialist gepland. We nemen contact op voor een passend tijdstip.</p>']],
            ['type' => 'tekst', 'inhoud' => ['html' => '<p>Heeft u vragen? Neem gerust contact op via {{ bedrijf.email }} of {{ bedrijf.telefoon }}.</p><p>Met vriendelijke groet,<br><strong>{{ bedrijf.naam }}</strong></p>']],
        ]);

        $template1 = OfferteTemplate::updateOrCreate(
            ['naam' => 'Zaptec Go — Standaard installatie'],
            ['beschrijving' => 'Template voor standaard installatie direct achter de meterkast', 'categorie' => 'laadpaal', 'document' => $standaardDoc]
        );
        $this->command->info('Template aangemaakt: ' . $template1->naam);

        $kruipDoc = $this->maakDocument([
            ['type' => 'tekst', 'inhoud' => ['html' => str_replace('Zaptec Go laadpaal', 'Zaptec Go laadpaal via kruipruimte', $introHtml)]],
            ['type' => 'prijstabel', 'inhoud' => []],
            ['type' => 'tekst', 'inhoud' => ['html' => '<h2>Werkwijze installatie via kruipruimte</h2><p>De kabel wordt via de kruipruimte geleid, wat een nette en onzichtbare aanleg oplevert.</p>']],
        ]);

        $template2 = OfferteTemplate::updateOrCreate(
            ['naam' => 'Zaptec Go — Installatie via kruipruimte'],
            ['beschrijving' => 'Template voor installatie met kabelrouting via kruipruimte', 'categorie' => 'laadpaal', 'document' => $kruipDoc]
        );
        $this->command->info('Template aangemaakt: ' . $template2->naam);
    }

    private function maakDocument(array $blokkens): array
    {
        $elementen = array_map(fn($blok) => [
            'id'   => Str::uuid()->toString(),
            'type' => $blok['type'],
            'instellingen' => [
                'achtergrond_kleur'    => null,
                'marge_top'            => 0,
                'marge_bottom'         => 24,
                'content_breedte_pct'  => 100,
                'content_offset_pct'   => 0,
            ],
            'inhoud' => $blok['inhoud'],
        ], $blokkens);

        return [
            'paginas' => [[
                'id'           => Str::uuid()->toString(),
                'instellingen' => [
                    'marge'                  => ['top' => 48, 'right' => 56, 'bottom' => 48, 'left' => 56],
                    'achtergrond_kleur'      => '#ffffff',
                    'achtergrond_afbeelding' => null,
                ],
                'elementen' => $elementen,
            ]],
        ];
    }
}
