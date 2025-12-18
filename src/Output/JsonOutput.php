<?php

namespace App\Output;

use App\Output\GradeOutputInterface;

class JsonOutput implements GradeOutputInterface
{
    public function __construct(private string $filePath) {}

    public function render(iterable $students): void
    {
        $handle = fopen($this->filePath, 'w');
        fwrite($handle, '[');

        $first = true;
        foreach ($students as $dto) {
            if (!$first) {
                fwrite($handle, ',');
            }

            fwrite($handle, json_encode([
                'studentId' => $dto->studentId,
                'score' => $dto->score,
                'grade' => $dto->grade,
                'passed' => $dto->passed,
            ]));

            $first = false;
        }

        fwrite($handle, ']');
        fclose($handle);
    }
}
