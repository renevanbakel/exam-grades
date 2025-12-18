<?php

namespace App\Output;

use Symfony\Component\Console\Style\SymfonyStyle;

class GradeOutputFactory
{
    public function create(string $format = 'table', SymfonyStyle $io): GradeOutputInterface
    {
        return match ($format) {
            'table' => new TableOutput($io),
            'csv'   => new CsvOutput('output.csv'),
            'json'  => new JsonOutput('output.json'),
            default => throw new \InvalidArgumentException("Unsupported output format: $format")
        };
    }
}
