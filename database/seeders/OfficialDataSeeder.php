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

            while (($data = fgetcsv($handle)) !== false) {
                // Basic validation: ensure email exists and is valid
                if (empty($data[4]) || !str_contains($data[4], '@')) {
                    continue;
                }

                $firstName = trim($data[0]);
                $lastName = trim($data[2]);
                $email = trim($data[4]);
                $officeName = trim($data[5]);

                // Find or create office
                $office = Office::where('name', $officeName)->first();
                if (!$office && !empty($officeName)) {
                    $office = Office::create([
                        'name' => $officeName,
                    ]);
                }

                Client::updateOrCreate(
                    ['email' => $email],
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'password' => Hash::make('Password123'), // Default secure password
                        'office_id' => $office ? $office->id : null,
                        'contact_number' => 'N/A',
                        'gallon_count' => 0
                    ]
                );
            }
            fclose($handle);
            $this->command->info("Imported users from {$filename}");
        }
    }
}
