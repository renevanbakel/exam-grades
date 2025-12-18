<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\GradeCommand;
use App\DTO\StudentGradeDTO;
use App\Output\GradeOutputFactory;
use App\Service\ExcelReaderService;
use App\Service\GradeCalculatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GradeCommandTest extends TestCase
{
    public function testExecuteWithTableOutput(): void
    {
        $excelReader = $this->createMock(ExcelReaderService::class);
        $gradeCalculator = new GradeCalculatorService();

        // Data format: header rows + data rows, first column student id, rest are question scores/max scores
        $excelReader->method('readExcelFile')->willReturn([
            // header row (ignored)
            ['Student', 'Q1', 'Q2'],
            // max scores row, used to compute maxScore = 5 + 5 = 10
            [null, 5, 5],
            // student rows
            ['s1', 5, 5], // 10/10 -> ratio 1.0 -> grade 10, passed
            ['s2', 2, 1], // 3/10 -> ratio 0.3 -> interpolated grade, not passed
        ]);

        $outputFactory = $this->createMock(GradeOutputFactory::class);

        $capturedStudents = [];
        $outputHandler = new class($capturedStudents) implements \App\Output\GradeOutputInterface {
            /** @var array<int, StudentGradeDTO> */
            private array $capturedStudentsLocal;

            public function __construct(array &$capturedStudents)
            {
                $this->capturedStudentsLocal = &$capturedStudents;
            }

            public function render(iterable $students): void
            {
                foreach ($students as $student) {
                    $this->capturedStudentsLocal[] = $student;
                }
            }
        };

        $outputFactory
            ->method('create')
            ->with('table', $this->anything())
            ->willReturn($outputHandler);

        $application = new Application();
        $application->addCommand(new GradeCommand($excelReader, $gradeCalculator, $outputFactory));

        $command = $application->find('app:calculate-grades');
        $tester = new CommandTester($command);

        $tester->execute([
            'file' => 'dummy.xlsx',
            '--output' => 'table',
        ]);

        $tester->assertCommandIsSuccessful();

        $this->assertCount(2, $capturedStudents);
        $this->assertInstanceOf(StudentGradeDTO::class, $capturedStudents[0]);
        $this->assertSame('s1', $capturedStudents[0]->studentId);
        $this->assertSame(10.0, $capturedStudents[0]->grade);
        $this->assertTrue($capturedStudents[0]->passed);

        $this->assertInstanceOf(StudentGradeDTO::class, $capturedStudents[1]);
        $this->assertSame('s2', $capturedStudents[1]->studentId);
        $this->assertFalse($capturedStudents[1]->passed);
    }
}


