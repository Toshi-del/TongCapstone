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

class ServicesReportExport
{
    protected $dateFrom;
    protected $dateTo;
    protected $serviceType;
    protected $startDate;
    protected $endDate;

    public function __construct($dateFrom, $dateTo, $serviceType)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->serviceType = $serviceType;
        $this->startDate = Carbon::parse($dateFrom)->startOfDay();
        $this->endDate = Carbon::parse($dateTo)->endOfDay();
    }

    /**
     * Generate and download the Excel file
     */
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $summaryData = [];
        $sheetIndex = 0;

        // Fetch and create Pre-Employment sheet
        if ($this->serviceType === 'all' || $this->serviceType === 'pre_employment') {
            $preEmploymentData = $this->fetchPreEmploymentData();
            
            if (!empty($preEmploymentData)) {
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Pre-Employment');
                $spreadsheet->addSheet($sheet, $sheetIndex++);
                $this->populateSheet($sheet, $preEmploymentData, 'Pre-Employment Records');
                
                $summaryData['Pre-Employment'] = [
                    'count' => count($preEmploymentData),
                    'total' => array_sum(array_column($preEmploymentData, 'Price'))
                ];
            }
        }

        // Fetch and create Annual Physical sheet
        if ($this->serviceType === 'all' || $this->serviceType === 'annual_physical') {
            $annualPhysicalData = $this->fetchAnnualPhysicalData();
            
            if (!empty($annualPhysicalData)) {
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Annual Physical');
                $spreadsheet->addSheet($sheet, $sheetIndex++);
                $this->populateSheet($sheet, $annualPhysicalData, 'Annual Physical Records');
                
                $summaryData['Annual Physical'] = [
                    'count' => count($annualPhysicalData),
                    'total' => array_sum(array_column($annualPhysicalData, 'Price'))
                ];
            }
        }

        // Fetch and create OPD sheet
        if ($this->serviceType === 'all' || $this->serviceType === 'opd') {
            $opdData = $this->fetchOPDData();
            
            if (!empty($opdData)) {
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'OPD');
                $spreadsheet->addSheet($sheet, $sheetIndex++);
                $this->populateSheet($sheet, $opdData, 'OPD Records');
                
                $summaryData['OPD'] = [
                    'count' => count($opdData),
                    'total' => array_sum(array_column($opdData, 'Price'))
                ];
            }
        }

        // Create Summary Sheet
        $summarySheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Summary');
        $spreadsheet->addSheet($summarySheet, $sheetIndex);
        $this->createSummarySheet($summarySheet, $summaryData);

        // Set the first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Generate filename
        $filename = 'services_report_' . $this->dateFrom . '_to_' . $this->dateTo . '_' . $this->serviceType . '.xlsx';

        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Fetch Pre-Employment data
     */
    protected function fetchPreEmploymentData()
    {
        $preEmploymentRecords = PreEmploymentRecord::with(['medicalTest', 'medicalTestCategory', 'creator'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        $data = [];
        foreach ($preEmploymentRecords as $record) {
            $testName = 'N/A';
            $categoryName = 'N/A';
            
            if ($record->medicalTest) {
                $testName = $record->medicalTest->name;
            }
            
            if ($record->medicalTestCategory) {
                $categoryName = $record->medicalTestCategory->name;
            }
            
            if ($testName === 'N/A' && $record->all_selected_tests && $record->all_selected_tests->isNotEmpty()) {
                $testName = $record->all_selected_tests->pluck('test_name')->implode(', ');
                $categoryName = $record->all_selected_tests->pluck('category_name')->unique()->implode(', ');
            }
            
            $data[] = [
                'Date' => $record->created_at->format('Y-m-d'),
                'Patient Name' => $record->first_name . ' ' . $record->last_name,
                'Email' => $record->email ?? 'N/A',
                'Contact' => $record->phone_number ?? 'N/A',
                'Company' => optional($record->creator)->company ?? 'N/A',
                'Test Name' => $testName,
                'Category' => $categoryName,
                'Price' => $record->total_price ?? 0,
                'Status' => ucfirst($record->status ?? 'pending'),
            ];
        }

        return $data;
    }

    /**
     * Fetch Annual Physical data
     */
    protected function fetchAnnualPhysicalData()
    {
        $appointments = Appointment::with(['patients', 'creator'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        $data = [];
        foreach ($appointments as $appointment) {
            $selectedTests = $appointment->selected_tests;
            
            foreach ($appointment->patients as $patient) {
                // Check if patient was age-adjusted
                $isAgeAdjusted = $patient->age_adjusted ?? false;
                $testPrice = 0;
                $testNames = [];
                $adjustedTestFound = false;
                
                // Calculate total price considering age adjustment
                foreach ($selectedTests as $test) {
                    // Check if this is the ECG+Drug Test that was adjusted
                    if ($isAgeAdjusted && stripos($test->name, 'Annual Medical with ECG and Drug test') !== false) {
                        // This test was adjusted - use reduced price
                        $testPrice += 750; // Adjusted price for Drug Test only (₱850 - ₱100)
                        $testNames[] = $patient->adjusted_test_name ?? 'Annual Medical with Drug Test';
                        $adjustedTestFound = true;
                    } else {
                        // Regular test - use normal price
                        $testPrice += $test->price ?? 0;
                        $testNames[] = $test->name;
                    }
                }
                
                // If no tests were found, use the default sum
                if (empty($testNames)) {
                    $testPrice = $selectedTests->sum('price');
                    $testNames = $selectedTests->pluck('name')->toArray();
                }
                
                $selectedCategories = $appointment->selected_categories;
                $categoryNames = $selectedCategories->pluck('name')->implode(', ');
                
                $data[] = [
                    'Date' => $appointment->created_at->format('Y-m-d'),
                    'Patient Name' => $patient->first_name . ' ' . $patient->last_name,
                    'Email' => $patient->email ?? 'N/A',
                    'Contact' => $patient->phone ?? 'N/A',
                    'Company' => optional($appointment->creator)->company ?? 'N/A',
                    'Test Name' => implode(', ', $testNames) ?: 'N/A',
                    'Category' => $categoryNames ?: 'N/A',
                    'Price' => $testPrice,
                    'Status' => ucfirst($appointment->status ?? 'pending'),
                ];
            }
        }

        return $data;
    }

    /**
     * Fetch OPD data (placeholder)
     */
    protected function fetchOPDData()
    {
        // Placeholder for OPD records
        // Add your OPD model logic here when ready
        return [];
    }

    /**
     * Populate a sheet with data and enhanced styling
     */
    protected function populateSheet($sheet, $data, $title)
    {
        // Set default row height for data rows (taller)
        $sheet->getDefaultRowDimension()->setRowHeight(35);
        
        // Title row
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:I1');
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
        $headers = ['Date', 'Patient Name', 'Email', 'Contact', 'Company', 'Test Name', 'Category', 'Price', 'Status'];
        $sheet->fromArray($headers, null, 'A2');
        $sheet->getRowDimension(2)->setRowHeight(35);

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ];
        $sheet->getStyle('A2:I2')->applyFromArray($headerStyle);

        // Data rows
        $row = 3;
        foreach ($data as $record) {
            $sheet->fromArray(array_values($record), null, 'A' . $row);
            
            // Set row height for data rows (taller for better readability)
            $sheet->getRowDimension($row)->setRowHeight(35);
            
            // Center align all cells with text wrapping
            $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            
            // Apply status color
            $this->applyStatusColor($sheet, 'I' . $row, $record['Status']);
            
            $row++;
        }

        // Totals row
        if (count($data) > 0) {
            $totalRow = $row;
            $sheet->getRowDimension($totalRow)->setRowHeight(35);
            $sheet->setCellValue('A' . $totalRow, 'TOTAL');
            $sheet->mergeCells('A' . $totalRow . ':G' . $totalRow);
            $sheet->setCellValue('H' . $totalRow, '=SUM(H3:H' . ($totalRow - 1) . ')');
            
            $totalStyle = [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ];
            $sheet->getStyle('A' . $totalRow . ':I' . $totalRow)->applyFromArray($totalStyle);
        }

        // Format price column
        if ($row > 3) {
            $sheet->getStyle('H3:H' . ($row - 1))->getNumberFormat()->setFormatCode('₱#,##0.00');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('₱#,##0.00');
        }

        // Auto-size columns based on content (prevents text cutoff)
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set minimum widths to ensure readability
        $minWidths = [
            'A' => 15,  // Date
            'B' => 25,  // Patient Name
            'C' => 30,  // Email
            'D' => 18,  // Contact
            'E' => 25,  // Company
            'F' => 40,  // Test Name (wider for long test names)
            'G' => 25,  // Category
            'H' => 15,  // Price
            'I' => 15,  // Status
        ];

        // Apply minimum widths after auto-sizing
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
            $sheet->getStyle('A2:I' . $row)->applyFromArray($borderStyle);
        }
    }

    /**
     * Apply color to status cells based on status value
     */
    protected function applyStatusColor($sheet, $cell, $status)
    {
        $statusLower = strtolower($status);
        $colors = [
            'approved' => 'D1FAE5',      // Light green
            'pending' => 'FEF3C7',       // Light yellow
            'declined' => 'FEE2E2',      // Light red
            'cancelled' => 'F3F4F6',     // Light gray
            'completed' => 'DBEAFE',     // Light blue
            'sent_to_company' => 'E0E7FF', // Light indigo
        ];

        $textColors = [
            'approved' => '065F46',      // Dark green
            'pending' => '92400E',       // Dark yellow
            'declined' => '991B1B',      // Dark red
            'cancelled' => '374151',     // Dark gray
            'completed' => '1E40AF',     // Dark blue
            'sent_to_company' => '3730A3', // Dark indigo
        ];

        $bgColor = $colors[$statusLower] ?? 'FFFFFF';
        $textColor = $textColors[$statusLower] ?? '000000';

        $statusStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => $textColor]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ];

        $sheet->getStyle($cell)->applyFromArray($statusStyle);
    }

    /**
     * Create summary sheet with enhanced styling
     */
    protected function createSummarySheet($sheet, $summaryData)
    {
        // Set default row height (taller)
        $sheet->getDefaultRowDimension()->setRowHeight(35);
        
        // Title
        $sheet->setCellValue('A1', 'Services Report Summary');
        $sheet->mergeCells('A1:D1');
        $sheet->getRowDimension(1)->setRowHeight(45);
        
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ];
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Date range
        $sheet->setCellValue('A2', 'Period: ' . $this->dateFrom . ' to ' . $this->dateTo);
        $sheet->mergeCells('A2:D2');
        $sheet->getRowDimension(2)->setRowHeight(35);
        
        $dateStyle = [
            'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6B7280']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ];
        $sheet->getStyle('A2')->applyFromArray($dateStyle);

        // Headers
        $sheet->setCellValue('A4', 'Service Type');
        $sheet->setCellValue('B4', 'Total Records');
        $sheet->setCellValue('C4', 'Total Revenue');
        $sheet->setCellValue('D4', 'Average per Record');
        $sheet->getRowDimension(4)->setRowHeight(35);

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ];
        $sheet->getStyle('A4:D4')->applyFromArray($headerStyle);

        // Summary data
        $row = 5;
        $grandTotalRecords = 0;
        $grandTotalRevenue = 0;

        foreach ($summaryData as $serviceType => $data) {
            $sheet->getRowDimension($row)->setRowHeight(35);
            $sheet->setCellValue('A' . $row, $serviceType);
            $sheet->setCellValue('B' . $row, $data['count']);
            $sheet->setCellValue('C' . $row, $data['total']);
            $average = $data['count'] > 0 ? $data['total'] / $data['count'] : 0;
            $sheet->setCellValue('D' . $row, $average);

            // Center align with text wrapping
            $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);

            $grandTotalRecords += $data['count'];
            $grandTotalRevenue += $data['total'];
            $row++;
        }

        // Grand totals
        if (!empty($summaryData)) {
            $totalRow = $row;
            $sheet->getRowDimension($totalRow)->setRowHeight(40);
            $sheet->setCellValue('A' . $totalRow, 'GRAND TOTAL');
            $sheet->setCellValue('B' . $totalRow, $grandTotalRecords);
            $sheet->setCellValue('C' . $totalRow, $grandTotalRevenue);
            $grandAverage = $grandTotalRecords > 0 ? $grandTotalRevenue / $grandTotalRecords : 0;
            $sheet->setCellValue('D' . $totalRow, $grandAverage);

            $totalStyle = [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '92400E']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ];
            $sheet->getStyle('A' . $totalRow . ':D' . $totalRow)->applyFromArray($totalStyle);

            // Format currency columns
            $sheet->getStyle('C5:C' . $totalRow)->getNumberFormat()->setFormatCode('₱#,##0.00');
            $sheet->getStyle('D5:D' . $totalRow)->getNumberFormat()->setFormatCode('₱#,##0.00');

            // Add borders
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ];
            $sheet->getStyle('A4:D' . $totalRow)->applyFromArray($borderStyle);
        }

        // Auto-size columns based on content
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set minimum widths
        $minWidths = [
            'A' => 30,  // Service Type
            'B' => 20,  // Total Records
            'C' => 20,  // Total Revenue
            'D' => 25,  // Average per Record
        ];

        foreach ($minWidths as $col => $minWidth) {
            $currentWidth = $sheet->getColumnDimension($col)->getWidth();
            if ($currentWidth === -1 || $currentWidth < $minWidth) {
                $sheet->getColumnDimension($col)->setWidth($minWidth);
            }
        }
    }
}
