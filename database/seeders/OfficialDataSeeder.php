<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Office;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OfficialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cleanup test clients
        Client::whereIn('email', ['client@dnsc.edu.ph', 'customer@dnsc.edu.ph'])->delete();

        $files = [
            'BASD_HRIS_REQUEST (1).csv',
            'Official_Data.csv'
        ];

        foreach ($files as $filename) {
            $path = base_path('excel/' . $filename);
            
            if (!file_exists($path)) {
                $this->command->warn("File not found: {$filename}");
                continue;
            }

            $handle = fopen($path, 'r');
            $header = fgetcsv($handle); // Skip header

            $offices = Office::all()->pluck('id', 'name')->toArray();
            $clientData = [];
            $batchSize = 100;

            while (($data = fgetcsv($handle)) !== false) {
                // Basic validation: ensure email exists and is valid
                if (empty($data[4]) || !str_contains($data[4], '@')) {
                    continue;
                }

                $firstName = trim($data[0]);
                $lastName = trim($data[2]);
                $email = trim($data[4]);
                $officeName = trim($data[5]);

                // Find or create office with local cache
                if (!empty($officeName) && !isset($offices[$officeName])) {
                    $office = Office::create(['name' => $officeName]);
                    $offices[$officeName] = $office->id;
                }

                $clientData[] = [
                    'email' => $email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => Hash::make('Password123'), // Default secure password
                    'office_id' => $offices[$officeName] ?? null,
                    'contact_number' => 'N/A',
                    'gallon_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($clientData) >= $batchSize) {
                    Client::upsert($clientData, ['email'], ['first_name', 'last_name', 'office_id', 'updated_at']);
                    $clientData = [];
                }
            }
            
            if (!empty($clientData)) {
                Client::upsert($clientData, ['email'], ['first_name', 'last_name', 'office_id', 'updated_at']);
            }

            fclose($handle);
            $this->command->info("Imported users from {$filename}");
        }
    }
}
