<?php

namespace App\Output;

use Symfony\Component\Console\Style\SymfonyStyle;

class TableOutput implements GradeOutputInterface
{
    public function __construct(private SymfonyStyle $io) {}

    public function render(iterable $students): void
    {
        $table = $this->io->createTable();
        $table->setHeaders(['Student ID', 'Score', 'Grade', 'Passed']);

        foreach ($students as $dto) {
            $table->addRow([
                $dto->studentId,
                $dto->score,
                $dto->grade,
                $dto->passed ? 'YES' : 'NO'
            ]);
        }

        $table->render();
    }
}
