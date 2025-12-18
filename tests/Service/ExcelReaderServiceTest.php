<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ExcelReaderService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class ExcelReaderServiceTest extends TestCase
{
    public function testReadExcelFileReturnsArrayOfRowsAndCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // populate a tiny sheet: 2x3 grid
        $sheet->setCellValue('A1', 'Student');
        $sheet->setCellValue('B1', 'Q1');
        $sheet->setCellValue('C1', 'Q2');

        $sheet->setCellValue('A2', 's1');
        $sheet->setCellValue('B2', 5);
        $sheet->setCellValue('C2', 3);

        $tempFile = tempnam(sys_get_temp_dir(), 'excel_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $service = new ExcelReaderService();
        $data = $service->readExcelFile($tempFile);

        $this->assertSame(
            [
                ['Student', 'Q1', 'Q2'],
                ['s1', 5, 3],
            ],
            $data
        );
    }
}


