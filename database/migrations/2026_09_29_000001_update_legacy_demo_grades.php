<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $demoGrades = [
            ['026-1001', 'ITE101', 88, 90, 91, 'released', 1.75, 1.50, 1.25],
            ['026-1002', 'ITE101', 82, 84, 86, 'submitted', 2.00, 2.25, 2.50],
            ['026-1001', 'ITE102', 94, 92, 95, 'released', 1.25, 1.50, 1.00],
            ['026-1002', 'ITE102', 78, 80, 82, 'draft', 2.75, 2.50, 2.25],
        ];

        foreach ($demoGrades as [$number, $code, $oldPrelim, $oldMidterm, $oldFinal, $status, $prelim, $midterm, $final]) {
            $grade = DB::table('grades')
                ->join('enrollments', 'grades.enrollment_id', '=', 'enrollments.id')
                ->join('students', 'enrollments.student_id', '=', 'students.id')
                ->join('class_assignments', 'enrollments.class_assignment_id', '=', 'class_assignments.id')
                ->join('subjects', 'class_assignments.subject_id', '=', 'subjects.id')
                ->where('students.student_number', $number)
                ->where('subjects.subject_code', $code)
                ->where('grades.prelim', $oldPrelim)
                ->where('grades.midterm', $oldMidterm)
                ->where('grades.final', $oldFinal)
                ->where('grades.status', $status)
                ->select('grades.id')
                ->first();

            if ($grade) {
                $computed = round(($prelim + $midterm + $final) / 3, 2);
                DB::table('grades')->where('id', $grade->id)->update([
                    'prelim' => $prelim,
                    'midterm' => $midterm,
                    'final' => $final,
                    'final_grade' => $computed,
                    'remarks' => $computed < 3.25 ? 'Passed' : 'Failed',
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Do not overwrite grade corrections made after this migration.
    }
};
