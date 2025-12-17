<?php

declare(strict_types = 1);

namespace App\Service;

class GradeCalculatorService
{
    private float $minScoreRatio = 0.2; // Score voor grade 1.0
    private float $passScoreRatio = 0.7; // Score voor grade 5.5
    private float $maxScoreRatio = 1.0; // Score voor grade 10.0

    private float $minGrade = 1.0;
    private float $passGrade = 5.5;
    private float $maxGrade = 10.0;

    public function calculateGradeAndPassStatus(float $score, int $maxScore): array
    {
        $ratio = $score / $maxScore;

        if ($ratio <= $this->minScoreRatio) {
            $grade = $this->minGrade;
        } elseif ($ratio < $this->passScoreRatio) {
            // Interpoleer tussen minGrade en passGrade bij gebrek aan score voor ration tussen 0.2 en 0.7
            $grade = $this->interpolate(
                $ratio,
                $this->minScoreRatio,
                $this->passScoreRatio,
                $this->minGrade,
                $this->passGrade
            );
        } elseif ($ratio < $this->maxScoreRatio) {
            $grade = $this->passGrade;
        } else {
            $grade = $this->maxGrade;
        }

        $grade = round($grade, 1);
        $passed = $ratio >= $this->passScoreRatio;

        return [$grade, $passed];
    }

    private function interpolate(float $value, float $fromMin, float $fromMax, float $toMin, float $toMax): float
    {
        return $toMin + (($value - $fromMin) / ($fromMax - $fromMin)) * ($toMax - $toMin);
    }
}
