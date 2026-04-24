<?php

namespace App\Http\Controllers;

use App\Models\Klant;
use App\Models\Offerte;
use App\Models\OfferteTemplate;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $producten = Product::actief()->orderBy('categorie')->orderBy('naam')->get();
        $template  = null;
        $initialRegels = [];

        if ($request->template_id) {
            $template = OfferteTemplate::with('regels')->find($request->template_id);
            if ($template) {
                $initialRegels = $template->regels->values()->map(fn ($r, $i) => [
                    'key'           => $i,
                    'product_id'    => $r->product_id,
                    'naam'          => $r->naam,
                    'beschrijving'  => $r->beschrijving ?? '',
                    'aantal'        => (int) $r->aantal,
                    'eenheidsprijs' => (float) $r->eenheidsprijs,
                ])->toArray();
            }
        }

        return view('offertes.create', compact('klanten', 'producten', 'template', 'initialRegels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'klant_id'              => 'required|exists:klanten,id',
            'inleiding'             => 'nullable|string',
            'geldig_tot'            => 'nullable|date',
            'regels'                => 'required|array|min:1',
            'regels.*.naam'         => 'required|string',
            'regels.*.beschrijving' => 'nullable|string',
            'regels.*.aantal'       => 'required|integer|min:1',
            'regels.*.eenheidsprijs'=> 'required|numeric|min:0',
        ]);

        $offerte = Offerte::create([
            'klant_id'    => $request->klant_id,
            'template_id' => $request->template_id ?: null,
            'inleiding'   => $request->inleiding,
            'geldig_tot'  => $request->geldig_tot,
            'status'      => 'concept',
        ]);

        foreach ($request->regels as $i => $regel) {
            $offerte->regels()->create([
                'product_id'    => $regel['product_id'] ?? null,
                'naam'          => $regel['naam'],
                'beschrijving'  => $regel['beschrijving'] ?? null,
                'aantal'        => $regel['aantal'],
                'eenheidsprijs' => $regel['eenheidsprijs'],
                'volgorde'      => $i,
            ]);
        }

        // Copy template secties → offerte secties
        if ($request->template_id) {
            $template = OfferteTemplate::with('secties')->find($request->template_id);
            if ($template) {
                foreach ($template->secties as $sectie) {
                    $offerte->secties()->create([
                        'type'     => $sectie->type,
                        'titel'    => $sectie->titel,
                        'inhoud'   => $sectie->inhoud ?? [],
                        'volgorde' => $sectie->volgorde,
                    ]);
                }
            }
        } else {
            // No template: generate minimal viewer secties
            $offerte->secties()->createMany([
                ['type' => 'voorblad',   'titel' => 'Voorblad',        'inhoud' => [],                                                                        'volgorde' => 0],
                ['type' => 'prijzen',    'titel' => 'Prijsoverzicht',   'inhoud' => [],                                                                        'volgorde' => 1],
                ['type' => 'acceptatie', 'titel' => 'Akkoord geven',    'inhoud' => ['tekst' => 'Ga je akkoord met deze offerte? Klik hieronder om te tekenen.'], 'volgorde' => 2],
            ]);
        }

        return redirect()->route('offertes.show', $offerte)->with('success', 'Offerte aangemaakt.');
    }

    public function show(Offerte $offerte)
    {
        $offerte->load('klant', 'regels');
        return view('offertes.show', compact('offerte'));
    }

    public function edit(Offerte $offerte)
    {
        $klanten   = Klant::orderBy('naam')->get();
        $producten = Product::actief()->orderBy('categorie')->orderBy('naam')->get();
        $offerte->load('klant', 'regels');

        $initialRegels = $offerte->regels->values()->map(fn ($r, $i) => [
            'key'           => $i,
            'product_id'    => $r->product_id,
            'naam'          => $r->naam,
            'beschrijving'  => $r->beschrijving ?? '',
            'aantal'        => (int) $r->aantal,
            'eenheidsprijs' => (float) $r->eenheidsprijs,
        ])->toArray();

        return view('offertes.edit', compact('offerte', 'klanten', 'producten', 'initialRegels'));
    }

    public function update(Request $request, Offerte $offerte)
    {
        // Quick status-only update (from show page "Versturen" button)
        if ($request->has('status') && !$request->has('regels')) {
            if ($request->status === 'verstuurd' && $offerte->status === 'concept') {
                $offerte->update(['status' => 'verstuurd', 'verstuurd_op' => now()]);
            }
            return redirect()->route('offertes.show', $offerte)->with('success', 'Offerte verstuurd.');
        }

        $request->validate([
            'inleiding'             => 'nullable|string',
            'geldig_tot'            => 'nullable|date',
            'regels'                => 'required|array|min:1',
            'regels.*.naam'         => 'required|string',
            'regels.*.beschrijving' => 'nullable|string',
            'regels.*.aantal'       => 'required|integer|min:1',
            'regels.*.eenheidsprijs'=> 'required|numeric|min:0',
        ]);

        $offerte->update([
            'inleiding'  => $request->inleiding,
            'geldig_tot' => $request->geldig_tot,
        ]);

        // Replace all regels
        $offerte->regels()->delete();
        foreach ($request->regels as $i => $regel) {
            $offerte->regels()->create([
                'product_id'    => $regel['product_id'] ?? null,
                'naam'          => $regel['naam'],
                'beschrijving'  => $regel['beschrijving'] ?? null,
                'aantal'        => $regel['aantal'],
                'eenheidsprijs' => $regel['eenheidsprijs'],
                'volgorde'      => $i,
            ]);
        }

        return redirect()->route('offertes.show', $offerte)->with('success', 'Offerte bijgewerkt.');
    }

    public function destroy(Offerte $offerte)
    {
        $offerte->delete();
        return redirect()->route('offertes.index')->with('success', 'Offerte verwijderd.');
    }

    public function editor(Offerte $offerte)
    {
        $offerte->load('klant', 'regels', 'secties');
        $producten = Product::actief()->orderBy('categorie')->orderBy('naam')->get();
        return view('offertes.editor', compact('offerte', 'producten'));
    }

    public function updateRegels(Request $request, Offerte $offerte)
    {
        $request->validate([
            'regels'                => 'required|array',
            'regels.*.naam'         => 'required|string',
            'regels.*.beschrijving' => 'nullable|string',
            'regels.*.aantal'       => 'required|integer|min:1',
            'regels.*.eenheidsprijs'=> 'required|numeric|min:0',
        ]);

        $offerte->regels()->delete();
        foreach ($request->regels as $i => $regel) {
            $offerte->regels()->create([
                'product_id'    => $regel['product_id'] ?? null,
                'naam'          => $regel['naam'],
                'beschrijving'  => $regel['beschrijving'] ?? null,
                'aantal'        => $regel['aantal'],
                'eenheidsprijs' => $regel['eenheidsprijs'],
                'volgorde'      => $i,
            ]);
        }

        $offerte->refresh();
        return response()->json([
            'regels'    => $offerte->regels->map(fn ($r) => [
                'id'            => $r->id,
                'product_id'    => $r->product_id,
                'naam'          => $r->naam,
                'beschrijving'  => $r->beschrijving ?? '',
                'aantal'        => (int) $r->aantal,
                'eenheidsprijs' => (float) $r->eenheidsprijs,
            ])->values(),
            'subtotaal' => (float) $offerte->subtotaal,
            'btw_bedrag'=> (float) $offerte->btw_bedrag,
            'totaal'    => (float) $offerte->totaal,
        ]);
    }

    // Publieke viewer voor klant
    public function viewer(string $token)
    {
        $offerte = Offerte::where('token', $token)->with('klant', 'regels', 'secties')->firstOrFail();

        if ($offerte->status === 'verstuurd') {
            $offerte->update(['status' => 'bekeken', 'bekeken_op' => now()]);
        }

        return view('offertes.viewer', compact('offerte'));
    }

    // Download PDF (publieke viewer)
    public function pdf(string $token)
    {
        $offerte = Offerte::where('token', $token)->with('klant', 'regels', 'secties')->firstOrFail();

        $pdf = Pdf::loadView('offertes.viewer-pdf', compact('offerte'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);

        $filename = $offerte->nummer . '.pdf';
        return $pdf->download($filename);
    }

    // Klant keurt offerte goed
    public function accepteer(Request $request, string $token)
    {
        $request->validate(
            [
                'naam'  => 'required|string|max:255',
                'akkoord' => 'accepted',
                'signature_type' => 'required|in:typed,drawn',
                'signature_data' => [
                    'required',
                    'string',
                    function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                        $type = (string) $request->input('signature_type');
                        $data = is_string($value) ? trim($value) : '';

                        if ($type === 'typed') {
                            if ($data === '') {
                                $fail('Vul uw handtekening in.');
                                return;
                            }
                            return;
                        }

                        if ($type === 'drawn') {
                            if ($data === '') {
                                $fail('Zet alstublieft uw handtekening in het tekenvak.');
                                return;
                            }
                            if (!str_starts_with($data, 'data:image/png;base64,')) {
                                $fail('De getekende handtekening is ongeldig. Probeer opnieuw te tekenen.');
                                return;
                            }
                            // Basic sanity check to avoid accepting an empty/invalid payload.
                            if (strlen($data) < 200) {
                                $fail('De getekende handtekening is te kort. Probeer opnieuw te tekenen.');
                                return;
                            }
                        }
                    },
                ],
                'email' => 'nullable|email|max:255',
            ],
            [
                'naam.required' => 'Vul uw naam in.',
                'naam.max' => 'Uw naam mag maximaal :max tekens bevatten.',
                'akkoord.accepted' => 'U moet akkoord gaan met dit voorstel en de van toepassing zijnde algemene voorwaarden.',
                'signature_type.required' => 'Kies een methode om te ondertekenen.',
                'signature_type.in' => 'Kies een geldige methode om te ondertekenen.',
                'signature_data.required' => 'Voeg alstublieft een handtekening toe.',
                'email.email' => 'Vul een geldig e-mailadres in.',
                'email.max' => 'Uw e-mailadres mag maximaal :max tekens bevatten.',
            ],
            [
                'naam' => 'naam',
                'email' => 'e-mailadres',
                'signature_type' => 'methode',
                'signature_data' => 'handtekening',
                'akkoord' => 'akkoord',
            ],
        );

        $offerte = Offerte::where('token', $token)->firstOrFail();

        if (!in_array($offerte->status, ['verstuurd', 'bekeken'])) {
            return back()->with('error', 'Deze offerte kan niet meer worden geaccepteerd.');
        }

        $offerte->update([
            'status'            => 'geaccepteerd',
            'geaccepteerd_op'   => now(),
            'geaccepteerd_door' => $request->naam,
            'geaccepteerd_akkoord' => true,
            'geaccepteerd_handtekening_type' => $request->signature_type,
            'geaccepteerd_handtekening' => $request->signature_data,
        ]);

        return back()->with('accepted', true);
    }
}
