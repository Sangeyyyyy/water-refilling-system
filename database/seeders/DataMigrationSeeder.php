<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campus;
use App\Models\Division;
use App\Models\Office;

class DataMigrationSeeder extends Seeder
{
    public function run(): void
    {
        $offices = Office::all();

        foreach ($offices as $office) {
            if ($office->campus && $office->division) {
                // Find or create campus
                $campus = Campus::firstOrCreate(['name' => $office->campus]);
                
                // Find or create division under that campus
                $division = Division::firstOrCreate([
                    'campus_id' => $campus->id,
                    'name' => $office->division
                ]);

                // Link office to division
                $office->division_id = $division->id;
                $office->save();
            }
        }
    }
}
