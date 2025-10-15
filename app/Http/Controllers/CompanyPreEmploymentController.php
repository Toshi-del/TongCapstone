<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\PreEmploymentRecord;
use App\Models\MedicalTestCategory;
use App\Models\MedicalTest;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;

class CompanyPreEmploymentController extends Controller
{
    public function index()
    {
        $files = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest'])
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('company.pre-employment.index', compact('files'));
    }

    public function create()
    {
        $medicalTestCategories = MedicalTestCategory::with(['activeMedicalTests' => function($query) {
            $query->distinct();
        }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->distinct()
            ->get();

        return view('company.pre-employment.create', compact('medicalTestCategories'));
    }

    public function store(Request $request)
    {
        \Log::info('=== PRE-EMPLOYMENT CREATION STARTED ===', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'N/A',
            'request_data' => $request->except(['excel_file']), // Exclude file from log
            'has_excel_file' => $request->hasFile('excel_file'),
            'timestamp' => now()
        ]);

        // Decode JSON arrays from frontend
        $categoryIds = json_decode($request->medical_test_categories_id, true) ?: [];
        $testIds = json_decode($request->medical_test_id, true) ?: [];

        \Log::info('Decoded arrays', [
            'category_ids' => $categoryIds,
            'test_ids' => $testIds
        ]);

        $validated = $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
            'package_other_exams' => 'nullable|string',
            'billing_type' => 'required|in:Patient,Company',
            'company_name' => 'required_if:billing_type,Company|nullable|string',
        ]);

        // Validate that we have matching arrays
        if (empty($categoryIds) || empty($testIds) || count($categoryIds) !== count($testIds)) {
            \Log::error('Invalid medical test selection', [
                'category_count' => count($categoryIds),
                'test_count' => count($testIds)
            ]);
            return back()
                ->withInput()
                ->withErrors(['medical_test_id' => 'Please select at least one medical test.']);
        }

        // Validate each test belongs to its corresponding category
        $selectedTests = [];
        $totalPrice = 0;
        
        for ($i = 0; $i < count($testIds); $i++) {
            $categoryId = $categoryIds[$i];
            $testId = $testIds[$i];
            
            $test = MedicalTest::find($testId);
            if (!$test || (int) $test->medical_test_category_id !== (int) $categoryId) {
                \Log::error('Test validation failed', [
                    'test_id' => $testId,
                    'category_id' => $categoryId,
                    'test_category_id' => $test->medical_test_category_id ?? null
                ]);
                return back()
                    ->withInput()
                    ->withErrors(['medical_test_id' => 'Selected medical test does not belong to the chosen category.']);
            }
            
            $selectedTests[] = $test;
            $totalPrice += $test->price ?? 0;
        }

        \Log::info('Selected tests breakdown', [
            'selected_tests' => array_map(function($test) {
                return [
                    'id' => $test->id,
                    'name' => $test->name,
                    'price' => $test->price,
                    'category_id' => $test->medical_test_category_id
                ];
            }, $selectedTests),
            'total_price_per_patient' => $totalPrice
        ]);

        \Log::info('Validation passed successfully', [
            'validated_data' => $validated,
            'selected_tests_count' => count($selectedTests),
            'total_price' => $totalPrice
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $processedRows = 0;
            $errorRows = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed

                // Validate required fields
                $errors = [];
                
                if (empty($row[0])) {
                    $errors[] = 'First Name is required';
                }
                
                if (empty($row[1])) {
                    $errors[] = 'Last Name is required';
                }
                
                // Handle separate age and sex fields
                $age = null;
                $sex = null;
                
                if (empty($row[2]) || !is_numeric($row[2])) {
                    $errors[] = 'Age must be a valid number';
                } else {
                    $age = (int) $row[2];
                }
                
                if (empty($row[3])) {
                    $errors[] = 'Sex is required';
                } else {
                    $sex = ucfirst(strtolower(trim($row[3])));
                    if (!in_array($sex, ['Male', 'Female'])) {
                        $errors[] = 'Sex must be Male or Female';
                    }
                }
                
                if (empty($row[4]) || !filter_var($row[4], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Valid Email is required';
                }
                
                if (empty($row[5])) {
                    $errors[] = 'Phone Number is required';
                }

                if (!empty($errors)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'errors' => $errors,
                        'data' => $row
                    ];
                } else {
                    // Check for duplicate records (same first name, last name, and email)
                    $firstName = trim($row[0]);
                    $lastName = trim($row[1]);
                    $email = trim($row[4]);
                    
                    $existingRecord = PreEmploymentRecord::where('first_name', $firstName)
                        ->where('last_name', $lastName)
                        ->where('email', $email)
                        ->where('created_by', Auth::id())
                        ->first();
                    
                    if ($existingRecord) {
                        $errorRows[] = [
                            'row' => $rowNumber,
                            'errors' => ['Duplicate record: A person with the same name and email already exists'],
                            'data' => $row
                        ];
                    } else {
                        $processedRows++;
                        
                        // Apply age-based ECG restriction for patients under 34
                        $adjustedTests = $selectedTests;
                        $adjustedCategoryIds = $categoryIds;
                        $adjustedTestIds = $testIds;
                        $adjustedTotalPrice = $totalPrice;
                        $originalPrice = $totalPrice;
                        $ageAdjusted = false;
                        
                        if ($age < 34) {
                            $newTests = [];
                            $newCategoryIds = [];
                            $newTestIds = [];
                            $newTotalPrice = 0;
                            
                            foreach ($adjustedTests as $index => $test) {
                                // Check if this is an ECG-related test
                                if (stripos($test->name, 'Pre-Employment with ECG and Drug test') !== false) {
                                    // Find the non-ECG equivalent
                                    $nonEcgTest = MedicalTest::where('name', 'Pre-Employment with Drug Test')
                                        ->where('medical_test_category_id', $test->medical_test_category_id)
                                        ->first();
                                    
                                    if ($nonEcgTest) {
                                        $newTests[] = $nonEcgTest;
                                        $newCategoryIds[] = $adjustedCategoryIds[$index];
                                        $newTestIds[] = $nonEcgTest->id;
                                        $newTotalPrice += $nonEcgTest->price ?? 0;
                                        $ageAdjusted = true;
                                        
                                        \Log::info('Age-based ECG restriction applied', [
                                            'patient_age' => $age,
                                            'original_test' => $test->name,
                                            'adjusted_test' => $nonEcgTest->name,
                                            'original_price' => $test->price,
                                            'adjusted_price' => $nonEcgTest->price
                                        ]);
                                    } else {
                                        // If no non-ECG equivalent found, keep original
                                        $newTests[] = $test;
                                        $newCategoryIds[] = $adjustedCategoryIds[$index];
                                        $newTestIds[] = $adjustedTestIds[$index];
                                        $newTotalPrice += $test->price ?? 0;
                                    }
                                } else {
                                    // Keep non-ECG tests as is
                                    $newTests[] = $test;
                                    $newCategoryIds[] = $adjustedCategoryIds[$index];
                                    $newTestIds[] = $adjustedTestIds[$index];
                                    $newTotalPrice += $test->price ?? 0;
                                }
                            }
                            
                            $adjustedTests = $newTests;
                            $adjustedCategoryIds = $newCategoryIds;
                            $adjustedTestIds = $newTestIds;
                            $adjustedTotalPrice = $newTotalPrice;
                        }
                        
                        // Save to database with total price of all selected medical tests
                        // Use the first selected test for the main fields
                        $firstTest = $adjustedTests[0];
                        $firstCategoryId = $adjustedCategoryIds[0];
                        
                        // Load all categories at once for efficiency
                        $categories = \App\Models\MedicalTestCategory::whereIn('id', $adjustedCategoryIds)->get()->keyBy('id');
                        
                        // Prepare selected tests data for other_exams field
                        $selectedTestsData = [];
                        foreach ($adjustedTests as $index => $test) {
                            $categoryId = $adjustedCategoryIds[$index];
                            $category = $categories->get($categoryId);
                            
                            $selectedTestsData[] = [
                                'category_id' => $categoryId,
                                'category_name' => $category->name ?? 'Unknown',
                                'test_id' => $adjustedTestIds[$index],
                                'test_name' => $test->name,
                                'price' => $test->price ?? 0,
                            ];
                        }
                        
                        // Combine package_other_exams with selected tests
                        $otherExamsData = [];
                        if (!empty($request->package_other_exams)) {
                            $otherExamsData['additional_exams'] = $request->package_other_exams;
                        }
                        if (count($adjustedTests) > 1) {
                            $otherExamsData['selected_tests'] = $selectedTestsData;
                        }
                        
                        $record = PreEmploymentRecord::create([
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'age' => $age,
                            'sex' => $sex,
                            'email' => $email,
                            'phone_number' => trim($row[5]),
                            'address' => !empty($row[6]) ? trim($row[6]) : null,
                            'medical_test_categories_id' => $firstCategoryId,
                            'medical_test_id' => $firstTest->id,
                            'total_price' => $adjustedTotalPrice,
                            'original_price' => $ageAdjusted ? $originalPrice : $adjustedTotalPrice,
                            'age_adjusted' => $ageAdjusted,
                            'other_exams' => !empty($otherExamsData) ? json_encode($otherExamsData) : $request->package_other_exams,
                            'billing_type' => $request->billing_type,
                            'company_name' => $request->company_name,
                            'uploaded_file' => $file->getClientOriginalName(),
                            'created_by' => Auth::id(),
                        ]);

                        \Log::info('Created pre-employment record with multiple tests in other_exams', [
                            'record_id' => $record->id,
                            'primary_test_id' => $firstTest->id,
                            'total_tests_count' => count($selectedTests),
                            'total_price' => $totalPrice
                        ]);
                        
                        // Notify company that pre-employment was submitted successfully
                        NotificationService::notifyCompanyPreEmploymentSubmitted($record, Auth::user());
                    }
                }
            }

            \Log::info('Excel processing completed', [
                'processed_rows' => $processedRows,
                'error_rows_count' => count($errorRows),
                'error_rows' => $errorRows
            ]);

            if ($processedRows > 0) {
                $message = "Successfully processed {$processedRows} pre-employment records.";
                if (!empty($errorRows)) {
                    $duplicateCount = count(array_filter($errorRows, function($error) {
                        return in_array('Duplicate record: A person with the same name and email already exists', $error['errors']);
                    }));
                    if ($duplicateCount > 0) {
                        $message .= " {$duplicateCount} duplicate records were skipped.";
                    }
                }
                
                // Create notification for admin
                $companyUser = Auth::user();
                $testNames = array_map(function($test) {
                    return $test->name;
                }, $selectedTests);
                
                Notification::createForAdmin(
                    'pre_employment_created',
                    'New Pre-Employment Records Created',
                    "Company '{$companyUser->company}' has created {$processedRows} new pre-employment record(s). Tests: " . implode(', ', $testNames) . ". Total value: ₱" . number_format($totalPrice * $processedRows, 2),
                    [
                        'company_name' => $companyUser->company,
                        'records_count' => $processedRows,
                        'tests' => $testNames,
                        'total_price_per_record' => $totalPrice,
                        'total_value' => $totalPrice * $processedRows,
                        'billing_type' => $request->billing_type,
                        'company_billing_name' => $request->company_name
                    ],
                    'medium',
                    $companyUser
                );
                
                \Log::info('=== PRE-EMPLOYMENT CREATION COMPLETED SUCCESSFULLY ===', [
                    'processed_rows' => $processedRows,
                    'message' => $message
                ]);
                
                return redirect()->route('company.pre-employment.index')
                    ->with('success', $message);
            } else {
                $errorMessage = 'No valid records found in the Excel file. Please check your data format.';
                if (!empty($errorRows)) {
                    $duplicateCount = count(array_filter($errorRows, function($error) {
                        return in_array('Duplicate record: A person with the same name and email already exists', $error['errors']);
                    }));
                    if ($duplicateCount > 0) {
                        $errorMessage = "All records were duplicates or had validation errors. {$duplicateCount} duplicate records were found.";
                    }
                }
                
                \Log::warning('No records processed', [
                    'error_message' => $errorMessage,
                    'error_rows' => $errorRows
                ]);
                
                return back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            \Log::error('=== PRE-EMPLOYMENT CREATION FAILED ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['excel_file'])
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $record = PreEmploymentRecord::with(['medicalTestCategory', 'medicalTest'])
            ->where('created_by', Auth::id())
            ->findOrFail($id);
        
        return view('company.pre-employment.show', compact('record'));
    }
} 