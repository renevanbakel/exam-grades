<?php

declare(strict_types=1);

namespace App\Tests\Output;

use App\DTO\StudentGradeDTO;
use App\Output\TableOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class TableOutputTest extends TestCase
{
    public function testRenderOutputsTableWithStudents(): void
    {
        $input = new ArrayInput([]);
        $buffer = new BufferedOutput();
        $io = new SymfonyStyle($input, $buffer);

        $output = new TableOutput($io);

        $students = [
            new StudentGradeDTO('s1', 80.0, 6.5, true),
            new StudentGradeDTO('s2', 40.0, 3.2, false),
        ];

        $output->render($students);

        $display = $buffer->fetch();

        $this->assertStringContainsString('Student ID', $display);
        $this->assertStringContainsString('Score', $display);
        $this->assertStringContainsString('Grade', $display);
        $this->assertStringContainsString('Passed', $display);
        $this->assertStringContainsString('s1', $display);
        $this->assertStringContainsString('s2', $display);
    }
}


