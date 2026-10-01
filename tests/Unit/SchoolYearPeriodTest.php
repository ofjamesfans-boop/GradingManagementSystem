<?php

namespace Tests\Unit;

use App\Models\SchoolYear;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class SchoolYearPeriodTest extends TestCase
{
    public function test_periods_follow_the_academic_calendar(): void
    {
        $this->assertSame(
            ['school_year' => '2025-2026', 'semester' => '2nd Semester'],
            SchoolYear::periodFor(CarbonImmutable::parse('2026-01-15'))
        );
        $this->assertSame(
            ['school_year' => '2025-2026', 'semester' => 'Summer'],
            SchoolYear::periodFor(CarbonImmutable::parse('2026-07-15'))
        );
        $this->assertSame(
            ['school_year' => '2026-2027', 'semester' => '1st Semester'],
            SchoolYear::periodFor(CarbonImmutable::parse('2026-08-01'))
        );
    }
}
