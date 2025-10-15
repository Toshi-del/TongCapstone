<?php

namespace App\Exports;

use App\Models\MedicalTest;
use App\Models\PreEmploymentRecord;
use App\Models\Appointment;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TopTestsExport
{
    /**
     * Generate and download the Excel file for Top Medical Tests
     */
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Top Medical Tests');

        // Generate data for last 90 days
        $since = Carbon::now()->subDays(90);
        
        $testsEarly = MedicalTest::select('id', 'name', 'medical_test_category_id', 'price')
            ->with('category')
            ->get();
            
        $testData = $testsEarly->map(function($test) use ($since) {
            // Pre-employment data
            $preEmploymentCount = PreEmploymentRecord::where('medical_test_id', $test->id)
                ->where('created_at', '>=', $since)
                ->count();
            $preEmploymentRevenue = (float) (PreEmploymentRecord::where('medical_test_id', $test->id)
                ->where('created_at', '>=', $since)
                ->sum('total_price') ?? 0);
            
            // Appointment data with patient counts
            $appointments = Appointment::where('medical_test_id', $test->id)
                ->where('created_at', '>=', $since)
                ->with('patients')
                ->get();
            $appointmentCount = $appointments->count();
            $totalPatients = $appointments->sum(function($appointment) {
                return $appointment->patients->count();
            });
            
            // Calculate appointment revenue using dynamic pricing
            $appointmentRevenue = 0;
            foreach($appointments as $appointment) {
                $patientCount = $appointment->patients->count();
                $testPrice = $test->price ?? 0;
                $appointmentRevenue += ($testPrice * $patientCount);
            }
            
            $totalCount = $preEmploymentCount + $appointmentCount;
            $totalRevenue = $preEmploymentRevenue + $appointmentRevenue;
            
            return [
                'Test Name' => $test->name,
                'Category' => optional($test->category)->name ?? 'N/A',
                'Price per Test' => $test->price ?? 0,
                'Pre-Employment Count' => $preEmploymentCount,
                'Appointment Count' => $appointmentCount,
                'Total Patients' => $totalPatients,
                'Total Tests' => $totalCount,
                'Pre-Employment Revenue' => round($preEmploymentRevenue, 2),
                'Appointment Revenue' => round($appointmentRevenue, 2),
                'Total Revenue' => round($totalRevenue, 2),
            ];
        })->filter(fn($row) => $row['Total Tests'] > 0)
          ->sortByDesc('Total Tests')
          ->take(20)
          ->values()
          ->toArray();

        $this->populateSheet($sheet, $testData, 'Top Medical Tests - Last 90 Days');

        // Generate filename
        $filename = 'top_medical_tests_' . now()->format('Y-m-d') . '.xlsx';

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
        $sheet->mergeCells('A1:J1');
        $sheet->getRowDimension(1)->setRowHeight(40);
        
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ];
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Headers
        $headers = [
            'Test Name', 'Category', 'Price per Test', 'Pre-Employment Count', 
            'Appointment Count', 'Total Patients', 'Total Tests', 
            'Pre-Employment Revenue', 'Appointment Revenue', 'Total Revenue'
        ];
        $sheet->fromArray($headers, null, 'A2');
        $sheet->getRowDimension(2)->setRowHeight(35);

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F97316']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ];
        $sheet->getStyle('A2:J2')->applyFromArray($headerStyle);

        // Data rows
        $row = 3;
        foreach ($data as $record) {
            $sheet->fromArray(array_values($record), null, 'A' . $row);
            $sheet->getRowDimension($row)->setRowHeight(35);
            
            // Center align all cells with text wrapping
            $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            
            $row++;
        }

        // Totals row
        if (count($data) > 0) {
            $totalRow = $row;
            $sheet->getRowDimension($totalRow)->setRowHeight(35);
            $sheet->setCellValue('A' . $totalRow, 'TOTAL');
            $sheet->mergeCells('A' . $totalRow . ':B' . $totalRow);
            $sheet->setCellValue('C' . $totalRow, ''); // Skip price column in totals
            $sheet->setCellValue('D' . $totalRow, '=SUM(D3:D' . ($totalRow - 1) . ')');
            $sheet->setCellValue('E' . $totalRow, '=SUM(E3:E' . ($totalRow - 1) . ')');
            $sheet->setCellValue('F' . $totalRow, '=SUM(F3:F' . ($totalRow - 1) . ')');
            $sheet->setCellValue('G' . $totalRow, '=SUM(G3:G' . ($totalRow - 1) . ')');
            $sheet->setCellValue('H' . $totalRow, '=SUM(H3:H' . ($totalRow - 1) . ')');
            $sheet->setCellValue('I' . $totalRow, '=SUM(I3:I' . ($totalRow - 1) . ')');
            $sheet->setCellValue('J' . $totalRow, '=SUM(J3:J' . ($totalRow - 1) . ')');
            
            $totalStyle = [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ];
            $sheet->getStyle('A' . $totalRow . ':J' . $totalRow)->applyFromArray($totalStyle);
        }

        // Format price and revenue columns
        if ($row > 3) {
            $sheet->getStyle('C3:C' . ($row - 1))->getNumberFormat()->setFormatCode('₱#,##0.00');
            $sheet->getStyle('H3:J' . $row)->getNumberFormat()->setFormatCode('₱#,##0.00');
        }

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum widths
        $minWidths = [
            'A' => 40,  // Test Name
            'B' => 25,  // Category
            'C' => 15,  // Price per Test
            'D' => 18,  // Pre-Employment Count
            'E' => 18,  // Appointment Count
            'F' => 15,  // Total Patients
            'G' => 12,  // Total Tests
            'H' => 20,  // Pre-Employment Revenue
            'I' => 18,  // Appointment Revenue
            'J' => 15,  // Total Revenue
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
            $sheet->getStyle('A2:J' . $row)->applyFromArray($borderStyle);
        }
    }
}
