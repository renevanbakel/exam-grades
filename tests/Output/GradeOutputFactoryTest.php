<?php

declare(strict_types=1);

namespace App\Tests\Output;

use App\Output\CsvOutput;
use App\Output\GradeOutputFactory;
use App\Output\JsonOutput;
use App\Output\TableOutput;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;

class GradeOutputFactoryTest extends TestCase
{
    private SymfonyStyle $io;
    private GradeOutputFactory $factory;

    protected function setUp(): void
    {
        $this->io = new SymfonyStyle(new ArrayInput([]), new NullOutput());
        $this->factory = new GradeOutputFactory();
    }

    public function testCreatesTableOutputByDefault(): void
    {
        $output = $this->factory->create('table', $this->io);

        $this->assertInstanceOf(TableOutput::class, $output);
    }

    public function testCreatesCsvOutput(): void
    {
        $output = $this->factory->create('csv', $this->io);

        $this->assertInstanceOf(CsvOutput::class, $output);
    }

    public function testCreatesJsonOutput(): void
    {
        $output = $this->factory->create('json', $this->io);

        $this->assertInstanceOf(JsonOutput::class, $output);
    }

    public function testUnsupportedFormatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported output format: xml');

        $this->factory->create('xml', $this->io);
    }
}


