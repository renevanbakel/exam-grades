<?php

namespace App\Service;

class GradeCalculatorService
{
    public function calculateGradeAndPassStatus(float $score, int $maxScore): array
    {
        $ratio = $score / $maxScore;

        // Grading based on assignment rules
        $grade = match (true) {
            $ratio <= 0.2 => 1.0,
            $ratio >= 0.7 && $ratio < 1.0 => 5.5,
            $ratio >= 1.0 => 10.0,
            default => 1.0, // Missing rule for 0.2 <> 0.7
        };

        $passed = $ratio >= 0.70;

        return [$grade, $passed];
    }
}
