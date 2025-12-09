<?php

declare(strict_types=1);

namespace App\Command;

use App\DTO\StudentGradeDTO;
use App\Service\ExcelReaderService;
use App\Service\GradeCalculatorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calculate-grades',
    description: 'Reads an Excel result sheet, calculates grades and pass status for all students.'
)]
class GradeCommand extends Command
{
    public function __construct(
        private readonly ExcelReaderService $excelReaderService,
        private readonly GradeCalculatorService $gradeCalculator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to the Excel file (e.g. data/Assignment.xlsx)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        $io->section('Loading results from Excel...');
        $data = $this->excelReaderService->readExcelFile($filePath);

        $io->text("Processing results for <info>" . count($data) . " rows</info>");
        
        $studentDtos = $this->processStudentScores($data);

        $io->success('Grades successfully calculated!');
        $io->newLine(2);

        $io->table(
            ['Student ID', 'Score', 'Grade', 'Passed'],
            array_map(fn(StudentGradeDTO $dto) => [
                $dto->studentId,
                $dto->score,
                $dto->grade,
                $dto->passed ? 'YES 🎉' : 'NO ❌'
            ], $studentDtos)
        );

        return Command::SUCCESS;
    }


    /**
     * Extracts all students from Excel and returns DTO list
     */
    private function processStudentScores(array $data, int $headerRows = 2, int $skipColumns = 1): array
    {
        $maxScore = $this->calculateMaxScore($data);

        $studentDtos = [];

        foreach ($data as $rowIndex => $row) {
            if ($rowIndex < $headerRows) continue; // Skip column headers + max score row

            $score = array_sum(array_slice($row, $skipColumns));

            [$grade, $passed] = $this->gradeCalculator->calculateGradeAndPassStatus($score, $maxScore);

            $studentDtos[] = new StudentGradeDto(
                studentId: (string)$row[0],
                score: $score,
                grade: $grade,
                passed: $passed
            );
        }

        return $studentDtos;
    }


    /**
     * Second Excel row contains max score per question.
     */
    private function calculateMaxScore(array $data): int
    {
        return array_sum(array_map('intval', $data[1]));
    }
}
