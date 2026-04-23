<?php

namespace App\Http\Controllers;

use App\Models\Offerte;
use App\Models\OfferteSectie;
use Illuminate\Http\Request;

class OfferteSectieController extends Controller
{
    public function store(Request $request, Offerte $offerte)
    {
        $request->validate(['type' => 'required|string', 'titel' => 'required|string']);

        $maxVolgorde = $offerte->secties()->max('volgorde') ?? -1;

        $sectie = $offerte->secties()->create([
            'type'     => $request->type,
            'titel'    => $request->titel,
            'inhoud'   => [],
            'volgorde' => $maxVolgorde + 1,
        ]);

        return response()->json($this->sectieData($sectie));
    }

    public function update(Request $request, Offerte $offerte, OfferteSectie $sectie)
    {
        abort_if($sectie->offerte_id !== $offerte->id, 403);

        $sectie->update([
            'titel'  => $request->titel,
            'inhoud' => $request->inhoud ?? [],
        ]);

        return response()->json($this->sectieData($sectie->fresh()));
    }

    public function destroy(Offerte $offerte, OfferteSectie $sectie)
    {
        abort_if($sectie->offerte_id !== $offerte->id, 403);
        $sectie->delete();
        return response()->json(['ok' => true]);
    }

    public function reorder(Request $request, Offerte $offerte)
    {
        foreach ($request->volgorde as $item) {
            $offerte->secties()->where('id', $item['id'])->update(['volgorde' => $item['volgorde']]);
        }
        return response()->json(['ok' => true]);
    }

    private function sectieData(OfferteSectie $s): array
    {
        return [
            'id'       => $s->id,
            'type'     => $s->type,
            'titel'    => $s->titel,
            'inhoud'   => $s->inhoud ?? (object) [],
            'volgorde' => $s->volgorde,
        ];
    }
}
