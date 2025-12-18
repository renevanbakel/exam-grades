<?php

declare(strict_types=1);

namespace App\Command;

use App\DTO\StudentGradeDTO;
use App\Output\GradeOutputFactory;
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
        private readonly GradeCalculatorService $gradeCalculator,
        private readonly GradeOutputFactory $outputFactory,
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
            )
            ->addOption(
                'output',
                'o',
                InputArgument::OPTIONAL,
                'Output format: table, csv, json',
                'table'
            );    
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        $io->section('Loading results from Excel...');
        $data = $this->excelReaderService->readExcelFile($filePath);

        $io->text("Processing results for <info>" . count($data) . " rows</info>");

        $outputFormat = $input->getOption('output');

        $outputHandler = $this->outputFactory->create($outputFormat, $io);
        $outputHandler->render(
            $this->generateStudentScores($data)
        );

        return Command::SUCCESS;
    }

    /**
     * @return \Generator<StudentGradeDTO>
     */
    private function generateStudentScores(array $data, int $headerRows = 2, int $skipColumns = 1): \Generator
    {
        $maxScore = 0;

        if($maxScore === 0) {
            $maxScore = $this->calculateMaxScore($data);
        }

        foreach ($data as $rowIndex => $row) {
            if ($rowIndex < $headerRows) continue;

            $score = array_sum(array_slice($row, $skipColumns));
            [$grade, $passed] = $this->gradeCalculator->calculateGradeAndPassStatus($score, $maxScore);

            yield new StudentGradeDTO(
                studentId: (string)$row[0],
                score: $score,
                grade: $grade,
                passed: $passed
            );
        }
    }

    /**
     * Second Excel row contains max score per question.
     */
    private function calculateMaxScore(array $data): int
    {
        return array_sum(array_map('intval', $data[1]));
    }
}
