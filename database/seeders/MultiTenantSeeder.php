<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MultiTenantSeeder extends Seeder
{
    public function run(): void
    {
        $team = Team::firstOrCreate(
            ['slug' => 'energx'],
            ['naam' => 'Energx', 'is_admin' => true]
        );

        foreach (['klanten', 'producten', 'offertes', 'offerte_templates', 'api_submissions', 'tickets'] as $table) {
            DB::table($table)->whereNull('team_id')->update(['team_id' => $team->id]);
        }

        DB::table('users')->whereNull('team_id')->update([
            'team_id'       => $team->id,
            'is_superadmin' => true,
        ]);

        if (!ApiKey::where('team_id', $team->id)->exists()) {
            ApiKey::generate($team->id, 'Energx Website');
        }

        $key = ApiKey::where('team_id', $team->id)->first();
        $this->command->info("Team '{$team->naam}' klaar (ID: {$team->id}).");
        $this->command->info("Energx API-sleutel: {$key->key}");
    }
}
