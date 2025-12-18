<?php

namespace App\Output;

use App\Output\GradeOutputInterface;

class CsvOutput implements GradeOutputInterface
{
    public function __construct(private string $filePath) {}

    public function render(iterable $students): void
    {
        $handle = fopen($this->filePath, 'w');

        fputcsv($handle, ['Student ID', 'Score', 'Grade', 'Passed'], ',', '"', '\\');

        foreach ($students as $dto) {
            fputcsv(
                $handle,
                [
                    $dto->studentId,
                    $dto->score,
                    $dto->grade,
                    $dto->passed ? 'YES' : 'NO',
                ],
                ',',
                '"',
                '\\'
            );
        }

        fclose($handle);
    }
}
