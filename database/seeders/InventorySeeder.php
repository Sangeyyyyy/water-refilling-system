<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function up(): void
    {
        Inventory::updateOrCreate(
            ['item_name' => 'Gallon'],
            ['stock_level' => 100, 'low_stock_threshold' => 20, 'unit' => 'pcs']
        );

        Inventory::updateOrCreate(
            ['item_name' => 'Cap'],
            ['stock_level' => 500, 'low_stock_threshold' => 50, 'unit' => 'pcs']
        );
    }

    /**
     * Alias for up() to match standard seeder run() method if needed,
     * though usually, we use run().
     */
    public function run(): void
    {
        $this->up();
    }
}
