<?php

namespace Tests\Feature;

use App\Models\Subject;
use Database\Seeders\SubjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_is_populated_and_repeated_seeding_preserves_edits(): void
    {
        $this->seed(SubjectSeeder::class);

        $this->assertDatabaseCount('subjects', 7);
        $this->assertDatabaseHas('subjects', [
            'subject_code' => 'ITE315',
            'subject_name' => 'Integrative Programming and Technologies 1',
            'units' => 3,
            'status' => 'active',
        ]);
        $this->assertSame(7, Subject::where('units', 3)->where('status', 'active')->count());

        $subject = Subject::where('subject_code', 'ITE101')->firstOrFail();
        $subject->update(['subject_name' => 'Updated Computing', 'units' => 4, 'status' => 'inactive']);
        Subject::create(['subject_code' => 'CUSTOM', 'subject_name' => 'Custom Subject', 'units' => 2, 'status' => 'active']);

        $this->seed(SubjectSeeder::class);

        $this->assertDatabaseCount('subjects', 8);
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'subject_name' => 'Updated Computing',
            'units' => 4,
            'status' => 'inactive',
        ]);
        $this->assertDatabaseHas('subjects', ['subject_code' => 'CUSTOM']);
    }
}
