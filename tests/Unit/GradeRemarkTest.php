<?php

namespace Tests\Unit;

use App\Models\Grade;
use PHPUnit\Framework\TestCase;

class GradeRemarkTest extends TestCase
{
    public function test_five_point_grades_use_the_correct_passing_threshold(): void
    {
        $this->assertSame('Passed', Grade::remark(2.33));
        $this->assertSame('Passed', Grade::remark(3.00));
        $this->assertSame('Failed', Grade::remark(3.25));
        $this->assertSame('Failed', Grade::remark(5.00));
    }

    public function test_existing_percentage_grades_still_work(): void
    {
        $this->assertSame('Passed', Grade::remark(75));
        $this->assertSame('Failed', Grade::remark(74));
    }
}
