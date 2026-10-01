<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ([
                '26-01' => 'DATA MINING',
                'ITE101' => 'Introduction to Computing',
                'ITE102' => 'Computer Programming 1',
                'ITE315' => 'Integrative Programming and Technologies 1',
                'ELEC3' => 'It Elective 3 (Data Mining)',
                'GE311' => 'Principles of Accounting',
                'ITE311' => 'Software Engineering',
            ] as $code => $name) {
                Subject::firstOrCreate(
                    ['subject_code' => $code],
                    ['subject_name' => $name, 'units' => 3.0, 'status' => 'active']
                );
            }
        });
    }
}
