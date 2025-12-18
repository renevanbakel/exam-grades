<?php

declare(strict_types=1);

namespace App\Tests\Output;

use App\DTO\StudentGradeDTO;
use App\Output\JsonOutput;
use PHPUnit\Framework\TestCase;

class JsonOutputTest extends TestCase
{
    public function testRenderWritesExpectedJsonArray(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'grades_json_');
        $output = new JsonOutput($tempFile);

        $students = [
            new StudentGradeDTO('s1', 80.0, 6.5, true),
            new StudentGradeDTO('s2', 40.0, 3.2, false),
        ];

        $output->render($students);

        $contents = file_get_contents($tempFile);
        $decoded = json_decode($contents, true);

        $this->assertCount(2, $decoded);
        $this->assertSame('s1', $decoded[0]['studentId']);
        $this->assertSame(80, $decoded[0]['score']);
        $this->assertSame(6.5, $decoded[0]['grade']);
        $this->assertTrue($decoded[0]['passed']);
        $this->assertSame('s2', $decoded[1]['studentId']);
        $this->assertSame(40, $decoded[1]['score']);
        $this->assertSame(3.2, $decoded[1]['grade']);
        $this->assertFalse($decoded[1]['passed']);

        @unlink($tempFile);
    }
}


