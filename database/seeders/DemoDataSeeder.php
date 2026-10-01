<?php

namespace Database\Seeders;

use App\Models\{ActivityLog, ClassAssignment, Enrollment, Grade, Instructor, SchoolYear, Section, Student, Subject, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Demo data must not be seeded in production.');
        }

        DB::transaction(function (): void {
            $admin = User::firstOrCreate(
                ['email' => 'admin@gradeflow.test'],
                ['name' => 'System Administrator', 'role' => 'administrator', 'status' => 'active', 'password' => Hash::make('password')]
            );

            $period = SchoolYear::activateCurrent(now());

            $sectionA = Section::firstOrCreate(
                ['section_name' => 'A', 'year_level' => '1st', 'program' => 'BSIT'],
                ['status' => 'active']
            );
            $sectionB = Section::firstOrCreate(
                ['section_name' => 'A', 'year_level' => '1st', 'program' => 'BEED'],
                ['status' => 'active']
            );

            $subjectOne = Subject::updateOrCreate(
                ['subject_code' => 'ITE101'],
                ['subject_name' => 'Introduction to Computing', 'units' => 3, 'status' => 'active']
            );
            $subjectTwo = Subject::updateOrCreate(
                ['subject_code' => 'ITE102'],
                ['subject_name' => 'Computer Programming 1', 'units' => 3, 'status' => 'active']
            );

            $facultyOneUser = User::updateOrCreate(
                ['email' => 'faculty.one@gradeflow.test'],
                ['name' => 'Maria Santos', 'role' => 'instructor', 'status' => 'active', 'password' => Hash::make('Faculty@123')]
            );
            $facultyTwoUser = User::updateOrCreate(
                ['email' => 'faculty.two@gradeflow.test'],
                ['name' => 'Jose Reyes', 'role' => 'instructor', 'status' => 'active', 'password' => Hash::make('Faculty@123')]
            );

            $facultyOne = Instructor::updateOrCreate(
                ['employee_number' => 'FAC-1001'],
                ['user_id' => $facultyOneUser->id, 'first_name' => 'Maria', 'middle_name' => null, 'last_name' => 'Santos', 'email' => $facultyOneUser->email, 'status' => 'active']
            );
            $facultyTwo = Instructor::updateOrCreate(
                ['employee_number' => 'FAC-1002'],
                ['user_id' => $facultyTwoUser->id, 'first_name' => 'Jose', 'middle_name' => null, 'last_name' => 'Reyes', 'email' => $facultyTwoUser->email, 'status' => 'active']
            );

            $studentOneUser = User::updateOrCreate(
                ['email' => 'student.one@gradeflow.test'],
                ['name' => 'Ana Cruz', 'role' => 'student', 'status' => 'active', 'password' => Hash::make('BCC1001')]
            );
            $studentTwoUser = User::updateOrCreate(
                ['email' => 'student.two@gradeflow.test'],
                ['name' => 'Mark Dela Rosa', 'role' => 'student', 'status' => 'active', 'password' => Hash::make('BCC1002')]
            );

            $studentOne = Student::updateOrCreate(
                ['student_number' => '026-1001'],
                ['user_id' => $studentOneUser->id, 'first_name' => 'Ana', 'middle_name' => null, 'last_name' => 'Cruz', 'email' => $studentOneUser->email, 'section_id' => $sectionA->id, 'status' => 'active']
            );
            $studentTwo = Student::updateOrCreate(
                ['student_number' => '026-1002'],
                ['user_id' => $studentTwoUser->id, 'first_name' => 'Mark', 'middle_name' => null, 'last_name' => 'Dela Rosa', 'email' => $studentTwoUser->email, 'section_id' => $sectionA->id, 'status' => 'active']
            );

            $classOne = ClassAssignment::firstOrCreate(
                ['subject_id' => $subjectOne->id, 'section_id' => $sectionA->id, 'school_year_id' => $period->id],
                ['instructor_id' => $facultyOne->id, 'status' => 'active']
            );
            $classTwo = ClassAssignment::firstOrCreate(
                ['subject_id' => $subjectTwo->id, 'section_id' => $sectionA->id, 'school_year_id' => $period->id],
                ['instructor_id' => $facultyTwo->id, 'status' => 'active']
            );

            $enrollments = [
                Enrollment::firstOrCreate(['student_id' => $studentOne->id, 'class_assignment_id' => $classOne->id], ['status' => 'active']),
                Enrollment::firstOrCreate(['student_id' => $studentTwo->id, 'class_assignment_id' => $classOne->id], ['status' => 'active']),
                Enrollment::firstOrCreate(['student_id' => $studentOne->id, 'class_assignment_id' => $classTwo->id], ['status' => 'active']),
                Enrollment::firstOrCreate(['student_id' => $studentTwo->id, 'class_assignment_id' => $classTwo->id], ['status' => 'active']),
            ];

            $gradeData = [
                [$enrollments[0], $facultyOneUser, 1.75, 1.50, 1.25, 'released'],
                [$enrollments[1], $facultyOneUser, 2.00, 2.25, 2.50, 'submitted'],
                [$enrollments[2], $facultyTwoUser, 1.25, 1.50, 1.00, 'released'],
                [$enrollments[3], $facultyTwoUser, 2.75, 2.50, 2.25, 'draft'],
            ];

            foreach ($gradeData as [$enrollment, $encoder, $prelim, $midterm, $final, $status]) {
                $average = Grade::compute($prelim, $midterm, $final);
                Grade::firstOrCreate(
                    ['enrollment_id' => $enrollment->id],
                    ['prelim' => $prelim, 'midterm' => $midterm, 'final' => $final, 'final_grade' => $average, 'remarks' => Grade::remark($average), 'status' => $status, 'encoded_by' => $encoder->id]
                );
            }

            ActivityLog::firstOrCreate(
                ['action' => 'demo.data.created', 'description' => 'System Administrator created the GradeFlow demonstration records.'],
                ['user_id' => $admin->id, 'created_at' => now()]
            );
        });
    }
}
