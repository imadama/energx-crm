<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReactie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketReactieController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'type'       => 'required|in:klant,intern,notitie',
            'bron'       => 'required|in:email,whatsapp,telefoon,portaal',
            'inhoud'     => 'required|string',
            'bijlagen.*' => 'nullable|file|max:10240',
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
            'type'         => $data['type'],
            'gebruiker_id' => in_array($data['type'], ['intern', 'notitie']) ? auth()->id() : null,
            'bron'         => $data['bron'],
            'inhoud'       => $data['inhoud'],
            'bijlagen'     => empty($bijlagen) ? null : $bijlagen,
        ]);

        // Status update based on reply type
        if ($data['type'] === 'klant' && $ticket->status === 'in afwachting') {
            $ticket->update(['status' => 'open']);
        } elseif ($data['type'] === 'intern') {
            $ticket->update(['status' => 'in afwachting']);
        }

        return back()->with('success', 'Reactie geplaatst.');
    }
}
