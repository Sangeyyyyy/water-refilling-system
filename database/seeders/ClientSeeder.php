<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Office;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a random office for the clients
        $office = Office::first();
        $officeId = $office ? $office->id : null;

        $clients = [
            [
                'first_name' => 'John',
                'last_name' => 'Client',
                'email' => 'client@dnsc.edu.ph',
                'password' => Hash::make('Password123'),
                'contact_number' => '09123456789',
                'office_id' => $officeId,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Customer',
                'email' => 'customer@dnsc.edu.ph',
                'password' => Hash::make('Password123'),
                'contact_number' => '09987654321',
                'office_id' => $officeId,
            ],
        ];

        foreach ($clients as $clientData) {
            Client::updateOrCreate(
                ['email' => $clientData['email']],
                $clientData
            );
        }
    }
}
