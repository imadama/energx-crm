<?php

namespace Database\Seeders;

use App\Models\Klant;
use App\Models\Offerte;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin gebruiker ──────────────────────────────────
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@energx.nl'],
            [
                'name'              => 'Admin',
                'email'             => 'admin@energx.nl',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );
        $this->command->info('Gebruiker aangemaakt: admin@energx.nl / password');

        // ── Producten ────────────────────────────────────────
        $zaptec = Product::updateOrCreate(
            ['naam' => 'Zaptec Go - 22 kW'],
            [
                'beschrijving' => null,
                'prijs'        => 599.00,
                'categorie'    => 'laadpaal',
                'merk'         => 'Zaptec',
                'actief'       => true,
            ]
        );

        $installatieKruip = Product::updateOrCreate(
            ['naam' => 'Standaard installatie (kruipruimte)'],
            [
                'beschrijving' => "- Wandmontage\n- Aardlekautomaat incl plaatsing\n- Tot 10m Voedings- en signaalkabel\n- Kabel via kruipruimte, incl 1m graaf/straatwerk",
                'prijs'        => 489.00,
                'categorie'    => 'installatie',
                'merk'         => null,
                'actief'       => true,
            ]
        );

        $installatieStandaard = Product::updateOrCreate(
            ['naam' => 'Standaard installatie (direct achter de meterkast)'],
            [
                'beschrijving' => "- Wandmontage\n- Aardlekautomaat incl plaatsing\n- Tot 10m Voedings- en signaalkabel",
                'prijs'        => 399.00,
                'categorie'    => 'installatie',
                'merk'         => null,
                'actief'       => true,
            ]
        );
        $this->command->info('Producten aangemaakt: ' . Product::count() . ' stuks');

        // ── Offerte templates ────────────────────────────────
        $this->call(ZaptecTemplatesSeeder::class);

        // ── Demo klant ───────────────────────────────────────
        $klant = Klant::updateOrCreate(
            ['email' => 'imadamazyan@gmail.com'],
            [
                'naam'       => 'Imad Amazyan',
                'telefoon'   => '0685023112',
                'straat'     => 'Karel de Stoutestraat',
                'huisnummer' => '4',
                'postcode'   => '4205 HM',
                'stad'       => 'Gorinchem',
                'bron'       => 'website',
                'notities'   => null,
            ]
        );
        $this->command->info('Klant aangemaakt: ' . $klant->naam);

        // ── Demo offerte ─────────────────────────────────────
        $template = \App\Models\OfferteTemplate::where('naam', 'like', '%Standaard installatie%')->first();

        if (Offerte::where('nummer', 'ENX-2026-0001')->exists()) {
            $this->command->info('Demo offerte bestaat al, overgeslagen.');
            return;
        }

        $offerte = Offerte::create([
            'klant_id'    => $klant->id,
            'template_id' => $template?->id,
            'status'      => 'concept',
            'inleiding'   => null,
            'geldig_tot'  => now()->addDays(30),
        ]);

        // Regels
        $offerte->regels()->create([
            'product_id'    => $zaptec->id,
            'naam'          => $zaptec->naam,
            'beschrijving'  => 'Inclusief montagebeugel en 5 jaar garantie',
            'aantal'        => 1,
            'eenheidsprijs' => $zaptec->prijs,
            'volgorde'      => 0,
        ]);
        $offerte->regels()->create([
            'product_id'    => $installatieStandaard->id,
            'naam'          => $installatieStandaard->naam,
            'beschrijving'  => 'Inclusief aardlekautomaat en bekabeling',
            'aantal'        => 1,
            'eenheidsprijs' => $installatieStandaard->prijs,
            'volgorde'      => 1,
        ]);

        // Secties kopiëren van template
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

        $this->command->info('Demo offerte aangemaakt: ' . $offerte->nummer);
    }
}
