<?php

declare(strict_types = 1);

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\GradeCalculatorService;

class GradeCalculatorServiceTest extends TestCase
{
    private GradeCalculatorService $calculator;

    protected function setUp(): void
    {
        $this->calculator = new GradeCalculatorService();
    }

    public function testGradeAtMinimum(): void
    {
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(1, 100);
        $this->assertEquals(1.0, $grade);
        $this->assertFalse($passed);
    }

    public function testGradeAtPassThreshold(): void
    {
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(70, 100);
        $this->assertEquals(5.5, $grade);
        $this->assertTrue($passed);
    }

    public function testGradeAtMaximum(): void
    {
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(100, 100);
        $this->assertEquals(10.0, $grade);
        $this->assertTrue($passed);
    }

    public function testGradeInterpolation(): void
    {
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(45, 100);
        $this->assertGreaterThan(1.0, $grade);
        $this->assertLessThan(5.5, $grade);
        $this->assertFalse($passed);
    }
}
