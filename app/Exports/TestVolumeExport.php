<?php

namespace App\Exports;

use App\Models\PreEmploymentRecord;
use App\Models\Appointment;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TestVolumeExport
{
    /**
     * Generate and download the Excel file for Medical Test Volume
     */
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Medical Test Volume');

        // Generate data for last 12 months
        $months = collect(range(0, 11))->map(fn($i) => Carbon::now()->subMonths(11 - $i));
        
        $data = [];
        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            // Pre-employment count
            $preEmploymentCount = PreEmploymentRecord::whereBetween('created_at', [$start, $end])
                ->whereNotNull('medical_test_id')
                ->count();
            
            // Appointment patient count
            $appointments = Appointment::whereBetween('created_at', [$start, $end])
                ->whereNotNull('medical_test_id')
                ->with('patients')
                ->get();
            $appointmentPatients = $appointments->sum(function($appointment) {
                return $appointment->patients->count();
            });
            
            $totalTests = $preEmploymentCount + $appointmentPatients;
            
            $data[] = [
                'Month' => $month->format('M Y'),
                'Pre-Employment Tests' => $preEmploymentCount,
                'Appointment Tests' => $appointmentPatients,
                'Total Tests' => $totalTests,
            ];
        }

        $this->populateSheet($sheet, $data, 'Medical Test Volume - Last 12 Months');

        // Generate filename
        $filename = 'medical_test_volume_' . now()->format('Y-m-d') . '.xlsx';

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
        $headers = ['Month', 'Pre-Employment Tests', 'Appointment Tests', 'Total Tests'];
        $sheet->fromArray($headers, null, 'A2');
        $sheet->getRowDimension(2)->setRowHeight(35);

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
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
