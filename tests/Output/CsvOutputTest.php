<?php

declare(strict_types=1);

namespace App\Tests\Output;

use App\DTO\StudentGradeDTO;
use App\Output\CsvOutput;
use PHPUnit\Framework\TestCase;

class CsvOutputTest extends TestCase
{
    public function testRenderWritesExpectedCsv(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'grades_csv_');
        $output = new CsvOutput($tempFile);

        $students = [
            new StudentGradeDTO('s1', 80.0, 6.5, true),
            new StudentGradeDTO('s2', 40.0, 3.2, false),
        ];

        $output->render($students);

        $contents = file($tempFile, FILE_IGNORE_NEW_LINES);

        $this->assertSame(
            ['Student ID', 'Score', 'Grade', 'Passed'],
            str_getcsv($contents[0], ',', '"', '\\')
        );
        $this->assertSame(
            ['s1', '80', '6.5', 'YES'],
            str_getcsv($contents[1], ',', '"', '\\')
        );
        $this->assertSame(
            ['s2', '40', '3.2', 'NO'],
            str_getcsv($contents[2], ',', '"', '\\')
        );

        @unlink($tempFile);
    }
}


