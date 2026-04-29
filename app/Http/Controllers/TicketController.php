<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\Klant;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['contactpersoon.klant'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nummer', 'like', "%{$search}%")
                  ->orWhere('titel', 'like', "%{$search}%")
                  ->orWhereHas('contactpersoon', function ($q2) use ($search) {
                      $q2->where('voornaam', 'like', "%{$search}%")
                         ->orWhere('achternaam', 'like', "%{$search}%")
                         ->orWhereHas('klant', function ($q3) use ($search) {
                             $q3->where('naam', 'like', "%{$search}%");
                         });
                  })
                  ->orWhereHas('reacties', function ($q4) use ($search) {
                      $q4->where('inhoud', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default filter
            $query->whereIn('status', ['open', 'in afwachting']);
        }

        $tickets = $query->paginate(20)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function create(Request $request)
    {
        $klanten = Klant::with('contactpersonen')->orderBy('naam')->get();
        return view('tickets.create', compact('klanten'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contactpersoon_id' => 'required|exists:contactpersonen,id',
            'titel'             => 'required|string|max:255',
            'bron'              => 'required|in:email,whatsapp,telefoon,portaal',
            'inhoud'            => 'required|string',
            'bijlagen.*'        => 'nullable|file|max:10240',
        ]);

        $ticket = Ticket::create([
            'contactpersoon_id' => $data['contactpersoon_id'],
            'titel'             => $data['titel'],
            'status'            => 'open',
        ]);

        $bijlagen = [];
        if ($request->hasFile('bijlagen')) {
            foreach ($request->file('bijlagen') as $file) {
                $path = $file->store('tickets', 'public');
                $bijlagen[] = [
                    'naam' => $file->getClientOriginalName(),
                    'pad'  => $path,
                ];
            }
        }

        $ticket->reacties()->create([
            'type'   => 'klant',
            'bron'   => $data['bron'],
            'inhoud' => $data['inhoud'],
            'bijlagen' => empty($bijlagen) ? null : $bijlagen,
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket aangemaakt.');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['contactpersoon.klant', 'reacties.gebruiker']);
        return view('tickets.show', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status' => 'required|in:open,in afwachting,gesloten',
        ]);

        $ticket->update($data);

        return back()->with('success', 'Ticketstatus bijgewerkt.');
    }
}
