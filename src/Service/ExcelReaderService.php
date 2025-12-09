<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReaderService
{
    public function readExcelFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $data = [];

        $rowIndex = 0;

        foreach ($sheet->getRowIterator() as $row) {
            $data[$rowIndex] = [];
            $cellIndex = 0;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // include empty cells
        
            foreach ($cellIterator as $cell) {
                $data[$rowIndex][$cellIndex] = $cell->getValue();
                $cellIndex++;
            }
            $rowIndex++;
        }

        return $data;
    }
}
