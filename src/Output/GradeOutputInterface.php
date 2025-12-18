<?php

namespace App\Output;

use App\DTO\StudentGradeDTO;

interface GradeOutputInterface
{
    /**
     * @param iterable<StudentGradeDTO> $students
     */
    public function render(iterable $students): void;
}
