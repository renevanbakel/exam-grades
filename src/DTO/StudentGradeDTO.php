<?php

namespace App\DTO;

class StudentGradeDTO
{
    public function __construct(
        public string $studentId,
        public float $score,
        public float $grade,
        public bool $passed
    ) {}

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getGrade(): float
    {
        return $this->grade;
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }
}
