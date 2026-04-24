<?php

namespace App\Services;

use App\Models\Offerte;
use App\Models\OfferteTemplate;

class DocumentRenderer
{
    private bool $editMode;
    private ?TokenResolver $tokenResolver;

    public function __construct(bool $editMode = false, ?TokenResolver $tokenResolver = null)
    {
        $this->editMode      = $editMode;
        $this->tokenResolver = $tokenResolver;
    }

    public function renderOfferte(Offerte $offerte): string
    {
        $document = $offerte->document ?? $this->leegDocument();
        return $this->renderDocument($document, $offerte->regels);
    }

    public function renderTemplate(OfferteTemplate $template): string
    {
        $document = $template->document ?? $this->leegDocument();
        return $this->renderDocument($document, collect());
    }

    public function renderDocument(array $document, $regels = null): string
    {
        $html = '';
        foreach ($document['paginas'] ?? [] as $pagina) {
            $html .= $this->renderPagina($pagina, $regels);
        }
        return $html;
    }

    private function renderPagina(array $pagina, $regels): string
    {
        $inst      = $pagina['instellingen'] ?? [];
        $marge     = $inst['marge'] ?? ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40];
        $bgKleur   = $inst['achtergrond_kleur'] ?? '#ffffff';
        $bgAfb     = $inst['achtergrond_afbeelding'] ?? null;
        $pagina_id = $pagina['id'] ?? '';

        $stijl  = "background-color:{$bgKleur};";
        $stijl .= "padding:{$marge['top']}px {$marge['right']}px {$marge['bottom']}px {$marge['left']}px;";
        if ($bgAfb) {
            $stijl .= "background-image:url('" . e($bgAfb) . "');background-size:cover;background-position:center;";
        }

        $elementen_html = '';
        foreach ($pagina['elementen'] ?? [] as $element) {
            $elementen_html .= $this->renderElement($element, $regels, $pagina_id);
        }

        $data_attr = $this->editMode ? " data-pagina-id=\"{$pagina_id}\"" : '';

        return "<div class=\"a4-pagina\" style=\"{$stijl}\"{$data_attr}>{$elementen_html}</div>";
    }

    private function renderElement(array $element, $regels, string $pagina_id): string
    {
        $inst      = $element['instellingen'] ?? [];
        $type      = $element['type'] ?? 'tekst';
        $elem_id   = $element['id'] ?? '';
        $bgKleur   = $inst['achtergrond_kleur'] ?? null;
        $margeT    = $inst['marge_top'] ?? 0;
        $margeB    = $inst['marge_bottom'] ?? 16;
        $breedte   = $inst['content_breedte_pct'] ?? 100;
        $offset    = $inst['content_offset_pct'] ?? 0;

        $outer_stijl = "margin-top:{$margeT}px;margin-bottom:{$margeB}px;";
        if ($bgKleur) {
            $outer_stijl .= "background-color:{$bgKleur};";
        }

        $inner_stijl = "width:{$breedte}%;margin-left:{$offset}%;";

        $inhoud_html = $this->renderBlok($type, $element['inhoud'] ?? [], $regels, $elem_id, $pagina_id);

        $data_attr = $this->editMode ? " data-element-id=\"{$elem_id}\" data-type=\"{$type}\"" : '';

        return "<div class=\"document-element\" style=\"{$outer_stijl}\"{$data_attr}>"
             . "<div class=\"document-content\" style=\"{$inner_stijl}\">{$inhoud_html}</div>"
             . "</div>";
    }

    private function renderBlok(string $type, array $inhoud, $regels, string $elem_id, string $pagina_id): string
    {
        return match($type) {
            'tekst'            => $this->renderTekst($inhoud),
            'tekst_2kolommen'  => $this->renderTekst2Kolommen($inhoud),
            'tekst_afbeelding' => $this->renderTekstAfbeelding($inhoud),
            'afbeelding'       => $this->renderAfbeelding($inhoud),
            'prijstabel'       => $this->renderPrijstabel($regels),
            'standaard_tabel'  => $this->renderStandaardTabel($inhoud),
            default            => '',
        };
    }

    private function renderTekst(array $inhoud): string
    {
        $html = $inhoud['html'] ?? '';
        if ($this->tokenResolver) {
            $html = $this->tokenResolver->resolve($html);
        }
        return "<div class=\"blok-tekst\">{$html}</div>";
    }

    private function renderTekst2Kolommen(array $inhoud): string
    {
        $breedte = $inhoud['kolom1_breedte_pct'] ?? 50;
        $breedte2 = 100 - $breedte;
        $html1 = $inhoud['kolom1_html'] ?? '';
        $html2 = $inhoud['kolom2_html'] ?? '';
        if ($this->tokenResolver) {
            $html1 = $this->tokenResolver->resolve($html1);
            $html2 = $this->tokenResolver->resolve($html2);
        }
        return "<div class=\"blok-2kolommen\">"
             . "<div class=\"kolom\" style=\"width:{$breedte}%\">{$html1}</div>"
             . "<div class=\"kolom\" style=\"width:{$breedte2}%\">{$html2}</div>"
             . "</div>";
    }

    private function renderTekstAfbeelding(array $inhoud): string
    {
        $links    = $inhoud['afbeelding_links'] ?? false;
        $tkBreedte = $inhoud['tekst_breedte_pct'] ?? 60;
        $afbBreedte = 100 - $tkBreedte;
        $html     = $inhoud['tekst_html'] ?? '';
        $afbUrl   = $inhoud['afbeelding_url'] ?? '';
        if ($this->tokenResolver) {
            $html = $this->tokenResolver->resolve($html);
        }

        $tekst_col = "<div class=\"kolom\" style=\"width:{$tkBreedte}%\">{$html}</div>";
        $afb_col   = "<div class=\"kolom\" style=\"width:{$afbBreedte}%\">"
                   . ($afbUrl ? "<img src=\"" . e($afbUrl) . "\" style=\"max-width:100%;height:auto;\">" : '')
                   . "</div>";

        $cols = $links ? $afb_col . $tekst_col : $tekst_col . $afb_col;
        return "<div class=\"blok-2kolommen\">{$cols}</div>";
    }

    private function renderAfbeelding(array $inhoud): string
    {
        $url       = $inhoud['afbeelding_url'] ?? '';
        $uitlijning = $inhoud['uitlijning'] ?? 'center';
        $breedte   = $inhoud['breedte_pct'] ?? 100;

        if (!$url) return '<div class="blok-afbeelding blok-afbeelding--leeg"></div>';

        $align_map = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'];
        $justify   = $align_map[$uitlijning] ?? 'center';

        return "<div class=\"blok-afbeelding\" style=\"display:flex;justify-content:{$justify};\">"
             . "<img src=\"" . e($url) . "\" style=\"width:{$breedte}%;height:auto;\">"
             . "</div>";
    }

    private function renderPrijstabel($regels): string
    {
        if (!$regels || $regels->isEmpty()) {
            return '<div class="blok-prijstabel blok-prijstabel--leeg"><p>Geen regels.</p></div>';
        }

        $btwGroepen = [];
        $subtotaal  = 0;

        $rijen_html = '';
        foreach ($regels as $regel) {
            $type = $regel->type ?? 'product';

            if ($type === 'tekst') {
                $rijen_html .= "<tr class=\"regel-tekst\"><td colspan=\"5\" class=\"pt-tekst-cel\">"
                             . e($regel->naam)
                             . "</td></tr>";
                continue;
            }

            if ($type === 'subtotaal') {
                $rijen_html .= "<tr class=\"regel-subtotaal\"><td colspan=\"4\" class=\"pt-label\">Subtotaal</td>"
                             . "<td class=\"pt-bedrag\">€ " . number_format((float)$regel->totaal, 2, ',', '.') . "</td></tr>";
                continue;
            }

            if ($type === 'korting') {
                $rijen_html .= "<tr class=\"regel-korting\"><td colspan=\"4\"><em>" . e($regel->naam) . "</em></td>"
                             . "<td class=\"pt-bedrag\">− € " . number_format(abs((float)$regel->totaal), 2, ',', '.') . "</td></tr>";
                $subtotaal += (float)$regel->totaal;
                continue;
            }

            $optioneel = $regel->optioneel ?? false;
            $eenheid   = $regel->eenheid ?: 'st.';
            $btw       = $regel->btw_tarief ?? 21;
            $totaal    = (float)$regel->totaal;

            $subtotaal += $totaal;
            $btwGroepen[$btw] = ($btwGroepen[$btw] ?? 0) + round($totaal * $btw / 100, 2);

            $opt_badge = $optioneel ? '<span class="pt-optioneel">optioneel</span>' : '';
            $rijen_html .= "<tr class=\"regel-{$type}" . ($optioneel ? ' regel-optioneel' : '') . "\">"
                         . "<td class=\"pt-omschrijving\">" . e($regel->naam) . $opt_badge . ($regel->beschrijving ? "<br><small>" . e($regel->beschrijving) . "</small>" : '') . "</td>"
                         . "<td class=\"pt-aantal\">" . e($regel->aantal) . " " . e($eenheid) . "</td>"
                         . "<td class=\"pt-prijs\">€ " . number_format((float)$regel->eenheidsprijs, 2, ',', '.') . "</td>"
                         . "<td class=\"pt-btw\">" . e($btw) . "%</td>"
                         . "<td class=\"pt-totaal\">€ " . number_format($totaal, 2, ',', '.') . "</td>"
                         . "</tr>";
        }

        $btw_html = '';
        $totaal_btw = 0;
        foreach ($btwGroepen as $tarief => $bedrag) {
            $btw_html .= "<tr><td colspan=\"4\" class=\"pt-label\">BTW {$tarief}%</td>"
                       . "<td class=\"pt-bedrag\">€ " . number_format($bedrag, 2, ',', '.') . "</td></tr>";
            $totaal_btw += $bedrag;
        }

        $totaal = $subtotaal + $totaal_btw;

        return "<div class=\"blok-prijstabel\">"
             . "<table class=\"pt-tabel\">"
             . "<thead><tr>"
             . "<th class=\"pt-omschrijving\">Omschrijving</th>"
             . "<th class=\"pt-aantal\">Aantal</th>"
             . "<th class=\"pt-prijs\">Prijs</th>"
             . "<th class=\"pt-btw\">BTW</th>"
             . "<th class=\"pt-totaal\">Totaal</th>"
             . "</tr></thead>"
             . "<tbody>{$rijen_html}</tbody>"
             . "<tfoot>"
             . "<tr class=\"pt-subtotaal-rij\"><td colspan=\"4\" class=\"pt-label\">Subtotaal</td><td class=\"pt-bedrag\">€ " . number_format($subtotaal, 2, ',', '.') . "</td></tr>"
             . $btw_html
             . "<tr class=\"pt-totaal-rij\"><td colspan=\"4\" class=\"pt-label\"><strong>Totaal incl. BTW</strong></td><td class=\"pt-bedrag\"><strong>€ " . number_format($totaal, 2, ',', '.') . "</strong></td></tr>"
             . "</tfoot>"
             . "</table>"
             . "</div>";
    }

    private function renderStandaardTabel(array $inhoud): string
    {
        $kolommen = $inhoud['kolommen'] ?? [];
        $rijen    = $inhoud['rijen'] ?? [];

        if (empty($kolommen)) return '';

        $thead = '<thead><tr>' . implode('', array_map(fn($k) => '<th>' . e($k) . '</th>', $kolommen)) . '</tr></thead>';
        $tbody = '<tbody>';
        foreach ($rijen as $rij) {
            $tbody .= '<tr>' . implode('', array_map(fn($cel) => '<td>' . e($cel) . '</td>', $rij)) . '</tr>';
        }
        $tbody .= '</tbody>';

        return "<div class=\"blok-standaard-tabel\"><table>{$thead}{$tbody}</table></div>";
    }

    public static function leegDocument(): array
    {
        return [
            'paginas' => [
                [
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'instellingen' => [
                        'marge'                => ['top' => 40, 'right' => 50, 'bottom' => 40, 'left' => 50],
                        'achtergrond_kleur'    => '#ffffff',
                        'achtergrond_afbeelding' => null,
                    ],
                    'elementen' => [],
                ],
            ],
        ];
    }
}
