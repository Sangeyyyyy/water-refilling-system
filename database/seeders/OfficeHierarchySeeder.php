<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;
use Illuminate\Support\Facades\DB;

class OfficeHierarchySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing offices
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Office::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            'MAIN CAMPUS' => [
                'OFFICE OF THE PRESIDENT' => [
                    'Presidential Affairs Division',
                    'Records Management Unit',
                    'Gender and Development Unit',
                    'Alumni Affairs Unit',
                    'Motor Pool Services Unit',
                    'Other Units under the Office of the President',
                    'Board Secretary',
                    'College Secretarial Affairs Unit',
                    'Internal Audit Services Unit',
                    'Public Information Unit',
                    'Research Ethics Committee'
                ],
                'OFFICE OF THE VICE PRESIDENT FOR ACADEMIC AFFAIRS' => [
                    'Quality Assurance Division',
                    'Compliance and Standardization Unit',
                    'Accreditation and Assessment Unit',
                    'National and International Rankings Unit',
                    'Internationalization and External Affairs Division',
                    'Linkages Unit',
                    'Mobility Unit',
                    'Curriculum and Instruction Division',
                    'Student Development and Services Division',
                    'Scholarship and Grants Unit',
                    'Career and Job Placement Unit',
                    'Dormitory Services Unit',
                    'Student Discipline Unit',
                    'Sports Unit',
                    'Socio-cultural Unit',
                    'Student Organizations Unit',
                    'Student Publication Unit',
                    'Guidance, Counselling, and Testing Unit',
                    'Institutional Planning and Project Development Division',
                    'Planning, Monitoring, and Evaluation Unit',
                    'Project Development and Management Unit'
                ],
                'OFFICE OF THE VICE PRESIDENT FOR ADMINISTRATION AND FINANCE' => [
                    'Information and Communication Technology Services Division',
                    'Systems Development Unit',
                    'ICT Infrastructure Management Unit',
                    'Technical Support Services Unit',
                    'Finance Services Division',
                    'Accounting Services Unit',
                    'Budgeting Services Unit',
                    'Cashiering Services Unit',
                    'Administrative Services Division',
                    'Procurement Services Unit',
                    'Supply and Property Management Unit',
                    'General Services Unit',
                    'Security Services Unit',
                    'Health Services Unit',
                    'Business and Auxiliary Services Division',
                    'Real Property Enterprise Unit',
                    'Technology Commercialization Unit',
                    'Business, Merchandise, and Service Unit',
                    'Human Resource Management Division',
                    'Payroll and Benefits Unit',
                    'Rewards and Recognitions Unit',
                    'Learning, Development and Performance Management Unit',
                    'Recruitment, Selection, and Placement Unit',
                    'Legal Affairs Division',
                    'Legal Services Unit',
                    'Data Privacy Unit'
                ],
                'OFFICE OF THE VICE PRESIDENT FOR RESEARCH, DEVELOPMENT, AND EXTENSION' => [
                    'Research Division',
                    'Research Publication Unit',
                    'Extension Division',
                    'Community Affairs Unit',
                    'Extension Communication Support Unit',
                    'Knowledge and Technology Transfer Division',
                    'Intellectual Property and Knowledge Management Unit',
                    'Technology Transfer Unit',
                    'Technology Business Incubation Unit',
                    'Marine Biodiversity Research and Conservation Center',
                    'Mindanao Food Innovation Center',
                    'Halal Industry Research and Training Center',
                    'Gender Equality, Diversity, and Social Inclusion Center',
                    'Mindanao Center for Geoanalytics and Image Mining',
                    'Disaster Risk Reduction and Management Operation Center',
                    'Social Development and Human Security Studies Center'
                ],
                'OTHER ACADEMIC AND SUPPORT DIVISIONS (MAIN CAMPUS)' => [
                    'Records and Admission Division',
                    'Students’ Records Unit',
                    'Student Admission Services Unit',
                    'Learning Resource Division',
                    'Learning Resource Unit',
                    'IT and Multimedia Resource Unit',
                    'National Service Training Program Division',
                    'Civic Welfare Training Service (CWTS) Unit',
                    'Reserve Officers’ Training Corps (ROTC) Unit',
                    'Literacy Training Service (LTS) Unit',
                    'Academic Programs Development Unit',
                    'Academic Review and Assessment Unit',
                    'Academic Programs Monitoring and Evaluation Unit',
                    'Adult Education and Lifelong Learning Division',
                    'Adult Education Unit'
                ]
            ],
            'DNSC–IGACOS CAMPUS' => [
                'Administration and Finance Services Units' => ['General Unit'],
                'Academic and Student Affairs Units' => ['General Unit'],
                'Research, Development, and Extension Units' => ['General Unit']
            ],
            'DNSC–CARMEN CAMPUS' => [
                'Administration and Finance Services Units' => ['General Unit'],
                'Academic and Student Affairs Units' => ['General Unit'],
                'Research, Development, and Extension Units' => ['General Unit']
            ],
            'DNSC–STO. TOMAS CAMPUS' => [
                'Administration and Finance Services Units' => ['General Unit'],
                'Academic and Student Affairs Units' => ['General Unit'],
                'Research, Development, and Extension Units' => ['General Unit']
            ],
            'DNSC–TALAINGOD CAMPUS' => [
                'Administration and Finance Services Units' => ['General Unit'],
                'Academic and Student Affairs Units' => ['General Unit'],
                'Research, Development, and Extension Units' => ['General Unit']
            ]
        ];

        foreach ($data as $campus => $divisions) {
            foreach ($divisions as $division => $units) {
                foreach ($units as $unit) {
                    Office::create([
                        'name' => $unit,
                        'building' => 'N/A' // To be updated if needed
                    ]);
                }
            }
        }
    }
}
