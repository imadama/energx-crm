<?php

namespace App\Http\Controllers;

use App\Models\Klant;
use App\Models\Offerte;
use App\Models\OfferteRegel;
use App\Models\OfferteTemplate;
use App\Models\Product;
use App\Services\DocumentRenderer;
use App\Services\TokenResolver;
use Illuminate\Http\Request;

class OfferteController extends Controller
{
    public function index()
    {
        $offertes = Offerte::with('klant')->latest()->paginate(20);
        return view('offertes.index', compact('offertes'));
    }

    public function create(Request $request)
    {
        $klanten   = Klant::orderBy('naam')->get();
        $templates = OfferteTemplate::orderBy('naam')->get();
        return view('offertes.create', compact('klanten', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'klant_id'   => 'required|exists:klanten,id',
            'geldig_tot' => 'nullable|date',
            'template_id'=> 'nullable|exists:offerte_templates,id',
        ]);

        $document = null;
        if ($request->template_id) {
            $template = OfferteTemplate::find($request->template_id);
            $document = $template?->document;
        }

        $offerte = Offerte::create([
            'klant_id'    => $request->klant_id,
            'template_id' => $request->template_id ?: null,
            'geldig_tot'  => $request->geldig_tot,
            'status'      => 'concept',
            'document'    => $document ?? DocumentRenderer::leegDocument(),
        ]);

        return redirect()->route('offertes.editor', $offerte)->with('success', 'Offerte aangemaakt. Begin met bewerken.');
    }

    public function show(Offerte $offerte)
    {
        $offerte->load('klant', 'regels');
        return view('offertes.show', compact('offerte'));
    }

    public function edit(Offerte $offerte)
    {
        $klanten = Klant::orderBy('naam')->get();
        $offerte->load('klant');
        return view('offertes.edit', compact('offerte', 'klanten'));
    }

    public function update(Request $request, Offerte $offerte)
    {
        if ($request->has('status') && !$request->has('klant_id')) {
            if ($request->status === 'verstuurd' && $offerte->status === 'concept') {
                $offerte->update(['status' => 'verstuurd', 'verstuurd_op' => now()]);
            }
            return redirect()->route('offertes.show', $offerte)->with('success', 'Offerte verstuurd.');
        }

        $request->validate([
            'klant_id'   => 'required|exists:klanten,id',
            'geldig_tot' => 'nullable|date',
        ]);

        $offerte->update([
            'klant_id'   => $request->klant_id,
            'geldig_tot' => $request->geldig_tot,
        ]);

        return redirect()->route('offertes.show', $offerte)->with('success', 'Offerte bijgewerkt.');
    }

    public function destroy(Offerte $offerte)
    {
        $offerte->delete();
        return redirect()->route('offertes.index')->with('success', 'Offerte verwijderd.');
    }

    // ── Editor ────────────────────────────────────────────────────────────────

    public function editor(Offerte $offerte)
    {
        $offerte->load('klant', 'regels.product');
        $producten = Product::actief()->orderBy('categorie')->orderBy('naam')->get();
        $tokens    = TokenResolver::beschikbareTokens();

        if (!$offerte->document) {
            $offerte->update(['document' => DocumentRenderer::leegDocument()]);
        }

        return view('offertes.editor', compact('offerte', 'producten', 'tokens'));
    }

    public function updateDocument(Request $request, Offerte $offerte)
    {
        $request->validate(['document' => 'required|array']);
        $offerte->update(['document' => $request->document]);
        return response()->json(['ok' => true]);
    }

    // ── Prijstabel regels CRUD ────────────────────────────────────────────────

    public function storeRegel(Request $request, Offerte $offerte)
    {
        $request->validate([
            'naam'          => 'required|string',
            'type'          => 'required|in:product,vrije_regel,subtotaal,korting,tekst,optioneel',
            'aantal'        => 'nullable|numeric',
            'eenheid'       => 'nullable|string|max:30',
            'eenheidsprijs' => 'nullable|numeric',
            'btw_tarief'    => 'nullable|integer|in:0,9,21',
            'optioneel'     => 'nullable|boolean',
            'beschrijving'  => 'nullable|string',
            'product_id'    => 'nullable|exists:producten,id',
        ]);

        $volgorde = $offerte->regels()->max('volgorde') + 1;

        $regel = $offerte->regels()->create([
            'product_id'    => $request->product_id,
            'naam'          => $request->naam,
            'beschrijving'  => $request->beschrijving,
            'type'          => $request->type ?? 'product',
            'aantal'        => $request->aantal ?? 1,
            'eenheid'       => $request->eenheid ?? 'st.',
            'eenheidsprijs' => $request->eenheidsprijs ?? 0,
            'btw_tarief'    => $request->btw_tarief ?? 21,
            'optioneel'     => $request->boolean('optioneel'),
            'volgorde'      => $volgorde,
        ]);

        return response()->json($this->regelData($regel->fresh()));
    }

    public function updateRegel(Request $request, Offerte $offerte, OfferteRegel $regel)
    {
        abort_if($regel->offerte_id !== $offerte->id, 403);

        $regel->update($request->only([
            'naam', 'beschrijving', 'type', 'aantal', 'eenheid',
            'eenheidsprijs', 'btw_tarief', 'optioneel', 'product_id',
        ]));

        return response()->json($this->regelData($regel->fresh()));
    }

    public function destroyRegel(Offerte $offerte, OfferteRegel $regel)
    {
        abort_if($regel->offerte_id !== $offerte->id, 403);
        $regel->delete();
        return response()->json(['ok' => true]);
    }

    public function reorderRegels(Request $request, Offerte $offerte)
    {
        foreach ($request->volgorde as $item) {
            $offerte->regels()->where('id', $item['id'])->update(['volgorde' => $item['volgorde']]);
        }
        return response()->json(['ok' => true]);
    }

    // ── Viewer ────────────────────────────────────────────────────────────────

    public function viewer(string $token)
    {
        $offerte = Offerte::where('token', $token)->with('klant', 'regels')->firstOrFail();

        if ($offerte->status === 'verstuurd') {
            $offerte->update(['status' => 'bekeken', 'bekeken_op' => now()]);
        }

        $resolver = new TokenResolver($offerte);
        $renderer = new DocumentRenderer(false, $resolver);
        $document_html = $renderer->renderOfferte($offerte);

        return view('offertes.viewer', compact('offerte', 'document_html'));
    }

    public function accepteer(Request $request, string $token)
    {
        $request->validate([
            'naam'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $offerte = Offerte::where('token', $token)->firstOrFail();

        if (!in_array($offerte->status, ['verstuurd', 'bekeken'])) {
            return back()->with('error', 'Deze offerte kan niet meer worden geaccepteerd.');
        }

        $offerte->update([
            'status'            => 'geaccepteerd',
            'geaccepteerd_op'   => now(),
            'geaccepteerd_door' => $request->naam,
        ]);

        return back()->with('accepted', true);
    }

    // ── Upload afbeelding ─────────────────────────────────────────────────────

    public function uploadAfbeelding(Request $request)
    {
        $request->validate(['afbeelding' => 'required|image|max:5120']);
        $pad = $request->file('afbeelding')->store('document-afbeeldingen', 'public');
        return response()->json(['url' => '/storage/' . $pad]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function regelData(OfferteRegel $r): array
    {
        return [
            'id'            => $r->id,
            'product_id'    => $r->product_id,
            'naam'          => $r->naam,
            'beschrijving'  => $r->beschrijving ?? '',
            'type'          => $r->type,
            'aantal'        => (float) $r->aantal,
            'eenheid'       => $r->eenheid ?? 'st.',
            'eenheidsprijs' => (float) $r->eenheidsprijs,
            'btw_tarief'    => (int) $r->btw_tarief,
            'btw_bedrag'    => (float) $r->btw_bedrag,
            'totaal'        => (float) $r->totaal,
            'volgorde'      => (int) $r->volgorde,
            'optioneel'     => (bool) $r->optioneel,
        ];
    }
}
