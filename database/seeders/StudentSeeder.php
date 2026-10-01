<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Fictional initial records; assign sections through Edit Student.
        $names = [
            ['Ana', 'Reyes', 'Santos'],
            ['Miguel', 'Cruz', 'Garcia'],
            ['Sofia', 'Lopez', 'Reyes'],
            ['Gabriel', 'Santos', 'Cruz'],
            ['Isabella', 'Ramos', 'Mendoza'],
            ['Daniel', 'Torres', 'Ramos'],
            ['Chloe', 'Garcia', 'Torres'],
            ['Joshua', 'Mendoza', 'Flores'],
            ['Andrea', 'Flores', 'Villanueva'],
            ['Nathan', 'Reyes', 'Bautista'],
            ['Bianca', 'Cruz', 'Aquino'],
            ['Ethan', 'Lopez', 'Castillo'],
            ['Camille', 'Santos', 'Navarro'],
            ['Liam', 'Ramos', 'Rivera'],
            ['Julia', 'Torres', 'Mercado'],
            ['Noah', 'Garcia', 'Gonzales'],
            ['Angela', 'Mendoza', 'Fernandez'],
            ['Lucas', 'Flores', 'Dela Rosa'],
            ['Nicole', 'Reyes', 'Salazar'],
            ['Aaron', 'Cruz', 'Valdez'],
        ];

        DB::transaction(function () use ($names): void {
            foreach ($names as $index => [$firstName, $middleName, $lastName]) {
                $number = '026-'.(2001 + $index);
                if (Student::where('student_number', $number)->exists()) {
                    continue;
                }

                $email = 'student'.(2001 + $index).'@gradeflow.test';
                if (User::where('email', $email)->exists() || Student::where('email', $email)->exists()) {
                    throw new \RuntimeException("Cannot seed student {$number}: {$email} is already in use.");
                }

                $user = User::create([
                    'name' => $firstName.' '.$lastName,
                    'email' => $email,
                    'role' => 'student',
                    'status' => 'active',
                    'password' => Hash::make(Student::defaultPasswordFor($number)),
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'student_number' => $number,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'section_id' => null,
                    'status' => 'active',
                ]);
            }
        });
    }
}
