<?php

declare(strict_types = 1);

namespace App\Tests\Service;

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

    public function testBelowMinRatioCapsToMinGrade(): void
    {
        // ratio 0.1 < minScoreRatio (0.2) should give min grade 1.0 and not passed
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(10, 100);

        $this->assertSame(1.0, $grade);
        $this->assertFalse($passed);
    }

    public function testBetweenMinAndPassIsInterpolated(): void
    {
        // pick a ratio exactly in the middle between 0.2 and 0.7 -> expected grade is midpoint between 1.0 and 5.5
        $score = 45.0; // ratio = 0.45
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus($score, 100);

        $expected = 1.0 + ((0.45 - 0.2) / (0.7 - 0.2)) * (5.5 - 1.0);
        $expected = round($expected, 1);

        $this->assertSame($expected, $grade);
        $this->assertFalse($passed);
    }

    public function testExactlyAtPassRatioIsPassing(): void
    {
        // ratio 0.7 should be passing
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(70, 100);

        $this->assertSame(5.5, $grade);
        $this->assertTrue($passed);
    }

    public function testBetweenPassAndMaxKeepsPassGrade(): void
    {
        // ratio between 0.7 and 1.0 should keep grade at passGrade (5.5) but still be passing
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(85, 100);

        $this->assertSame(5.5, $grade);
        $this->assertTrue($passed);
    }

    public function testExactlyAtMaxRatioGetsMaxGrade(): void
    {
        // ratio 1.0 should give max grade 10.0 and be passing
        [$grade, $passed] = $this->calculator->calculateGradeAndPassStatus(100, 100);

        $this->assertSame(10.0, $grade);
        $this->assertTrue($passed);
    }
}
