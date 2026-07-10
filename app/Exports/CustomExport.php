<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomExport
{
    protected Spreadsheet $spreadsheet;
    protected array $columnWidths = [];

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    public function setColumnWidths(array $widths): self
    {
        $this->columnWidths = $widths;
        return $this;
    }

    public function writeHeader(string $title, string $subtitle = '', int $mergeToCol = 8): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->mergeCells($this->cell(1, 1) . ':' . $this->cell($mergeToCol, 1));
        $sheet->setCellValue($this->cell(1, 1), $title);
        $sheet->getStyle($this->cell(1, 1))->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle($this->cell(1, 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($subtitle) {
            $sheet->mergeCells($this->cell(1, 2) . ':' . $this->cell($mergeToCol, 2));
            $sheet->setCellValue($this->cell(1, 2), $subtitle);
            $sheet->getStyle($this->cell(1, 2))->getFont()->setSize(11);
            $sheet->getStyle($this->cell(1, 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    public function writeTableHeader(array $headers, int $startRow): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $col = 1;
        foreach ($headers as $header) {
            $cell = $this->cell($col, $startRow);
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9E2F3');
            $sheet->getStyle($cell)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cell)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }
    }

    public function writeRow(array $data, int $row): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $col = 1;
        foreach ($data as $value) {
            $cell = $this->cell($col, $row);
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cell)->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }
    }

    public function autoSizeColumns(int $startCol, int $endCol): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        foreach (range($startCol, $endCol) as $col) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
    }

    public function download(string $filename): StreamedResponse
    {
        $writer = new Xlsx($this->spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    protected function cell(int $col, int $row): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
    }
}
