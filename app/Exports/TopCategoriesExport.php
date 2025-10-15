<?php

namespace App\Exports;

use App\Models\MedicalTestCategory;
use App\Models\PreEmploymentRecord;
use App\Models\Appointment;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TopCategoriesExport
{
    /**
     * Generate and download the Excel file for Top Categories
     */
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Top Categories');

        // Generate data for last 90 days
        $since = Carbon::now()->subDays(90);
        
        $categoryData = MedicalTestCategory::select('id', 'name')
            ->get()
            ->map(function ($cat) use ($since) {
                // Pre-employment count
                $preEmploymentCount = PreEmploymentRecord::where('medical_test_categories_id', $cat->id)
                    ->where('created_at', '>=', $since)
                    ->count();
                
                // Appointment patient count
                $appointments = Appointment::where('medical_test_categories_id', $cat->id)
                    ->where('created_at', '>=', $since)
                    ->with('patients')
                    ->get();
                $appointmentPatients = $appointments->sum(function($appointment) {
                    return $appointment->patients->count();
                });
                
                $totalCount = $preEmploymentCount + $appointmentPatients;
                
                return [
                    'Category' => $cat->name,
                    'Pre-Employment Tests' => $preEmploymentCount,
                    'Appointment Tests' => $appointmentPatients,
                    'Total Tests' => $totalCount,
                ];
            })
            ->filter(fn($row) => $row['Total Tests'] > 0)
            ->sortByDesc('Total Tests')
            ->values()
            ->toArray();

        $this->populateSheet($sheet, $categoryData, 'Top Medical Test Categories - Last 90 Days');

        // Generate filename
        $filename = 'top_categories_' . now()->format('Y-m-d') . '.xlsx';

        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Populate sheet with data and styling
     */
    protected function populateSheet($sheet, $data, $title)
    {
        // Set default row height
        $sheet->getDefaultRowDimension()->setRowHeight(35);
        
        // Title row
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:D1');
        $sheet->getRowDimension(1)->setRowHeight(40);
        
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Headers
        $headers = ['Category', 'Pre-Employment Tests', 'Appointment Tests', 'Total Tests'];
        $sheet->fromArray($headers, null, 'A2');
        $sheet->getRowDimension(2)->setRowHeight(35);

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A2:D2')->applyFromArray($headerStyle);

        // Data rows
        $row = 3;
        foreach ($data as $record) {
            $sheet->fromArray(array_values($record), null, 'A' . $row);
            $sheet->getRowDimension($row)->setRowHeight(35);
            
            // Center align all cells
            $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            
            $row++;
        }

        // Totals row
        if (count($data) > 0) {
            $totalRow = $row;
            $sheet->getRowDimension($totalRow)->setRowHeight(35);
            $sheet->setCellValue('A' . $totalRow, 'TOTAL');
            $sheet->setCellValue('B' . $totalRow, '=SUM(B3:B' . ($totalRow - 1) . ')');
            $sheet->setCellValue('C' . $totalRow, '=SUM(C3:C' . ($totalRow - 1) . ')');
            $sheet->setCellValue('D' . $totalRow, '=SUM(D3:D' . ($totalRow - 1) . ')');
            
            $totalStyle = [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A' . $totalRow . ':D' . $totalRow)->applyFromArray($totalStyle);
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum widths
        $minWidths = [
            'A' => 30,  // Category
            'B' => 20,  // Pre-Employment Tests
            'C' => 20,  // Appointment Tests
            'D' => 15,  // Total Tests
        ];

        foreach ($minWidths as $col => $minWidth) {
            $currentWidth = $sheet->getColumnDimension($col)->getWidth();
            if ($currentWidth === -1 || $currentWidth < $minWidth) {
                $sheet->getColumnDimension($col)->setWidth($minWidth);
            }
        }

        // Add borders
        if ($row > 2) {
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ];
            $sheet->getStyle('A2:D' . $row)->applyFromArray($borderStyle);
        }
    }
}
