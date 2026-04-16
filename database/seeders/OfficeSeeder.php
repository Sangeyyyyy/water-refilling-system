<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            ['name' => 'Office of the College President', 'building' => 'Admin Building'],
            ['name' => 'Office of the VP for Academic Affairs (OVPAA)', 'building' => 'Admin Building'],
            ['name' => 'Office of the VP for Administration & Finance (OVPAF)', 'building' => 'Admin Building'],
            ['name' => 'Office of the VP for Research, Extension & Production (OVPREP)', 'building' => 'Admin Building'],
            ['name' => 'Office of Student Affairs & Services (OSAS)', 'building' => 'Student Center'],
            ['name' => 'Human Resource Management Office (HRMO)', 'building' => 'Admin Building'],
            ['name' => 'Supply & Property Management Office', 'building' => 'Admin Building'],
            ['name' => 'Cashier\'s Office', 'building' => 'Finance Building'],
            ['name' => 'Accounting Office', 'building' => 'Finance Building'],
            ['name' => 'Registrar\'s Office', 'building' => 'Admin Building'],
            ['name' => 'College Clinic', 'building' => 'Student Center'],
            ['name' => 'Guidance & Counseling Office', 'building' => 'Student Center'],
            ['name' => 'Institute of Computing (IC) - Dean\'s Office', 'building' => 'IT Building'],
            ['name' => 'Institute of Teacher Education (ITE) - Dean\'s Office', 'building' => 'Education Building'],
            ['name' => 'Institute of Leadership, Entrepreneurship & Governance (ILEG)', 'building' => 'ILEG Building'],
            ['name' => 'Institute of Aquatic and Applied Sciences (IAAS)', 'building' => 'Science Building'],
            ['name' => 'General Services Office (GSO)', 'building' => 'Maintenance Building'],
        ];

        foreach ($offices as $office) {
            Office::create($office);
        }
    }
}
