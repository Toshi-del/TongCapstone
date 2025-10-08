<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\DrugTestResult;

class PreEmploymentRecord extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'sex',
        'email',
        'phone_number',
        'address',
        'medical_test_categories_id',
        'medical_test_id',
        'total_price',
        'original_price',
        'age_adjusted',
        'other_exams',
        'billing_type',
        'company_name',
        'uploaded_file',
        'status',
        'registration_link_sent',
        'created_by',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'age_adjusted' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function preEmploymentExamination(): HasOne
    {
        return $this->hasOne(PreEmploymentExamination::class, 'pre_employment_record_id');
    }

    public function medicalTestCategory(): BelongsTo
    {
        return $this->belongsTo(MedicalTestCategory::class, 'medical_test_categories_id');
    }

    public function medicalTest(): BelongsTo
    {
        return $this->belongsTo(MedicalTest::class, 'medical_test_id');
    }

    public function medicalChecklist(): HasOne
    {
        return $this->hasOne(MedicalChecklist::class, 'pre_employment_record_id');
    }

    /**
     * Get the medical tests associated with this pre-employment record
     */
    public function preEmploymentMedicalTests(): HasMany
    {
        return $this->hasMany(PreEmploymentMedicalTest::class);
    }

    /**
     * Get the medical tests through the pivot table
     */
    public function medicalTests(): BelongsToMany
    {
        return $this->belongsToMany(MedicalTest::class, 'pre_employment_medical_tests')
                    ->withPivot('medical_test_category_id')
                    ->withTimestamps();
    }

    /**
     * Get the medical test categories through the pivot table
     */
    public function medicalTestCategories(): BelongsToMany
    {
        return $this->belongsToMany(MedicalTestCategory::class, 'pre_employment_medical_tests')
                    ->withPivot('medical_test_id')
                    ->withTimestamps();
    }

    /**
     * Get the drug test result associated with this pre-employment record
     */
    public function drugTest(): HasOne
    {
        return $this->hasOne(DrugTestResult::class, 'pre_employment_record_id');
    }

    // Helper method to parse other_exams JSON data
    public function getParsedOtherExamsAttribute()
    {
        if (empty($this->other_exams)) {
            return null;
        }

        // Try to decode as JSON first
        $decoded = json_decode($this->other_exams, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // If not JSON, return as plain text
        return ['additional_exams' => $this->other_exams];
    }

    // Helper method to get all selected tests (including from other_exams)
    public function getAllSelectedTestsAttribute()
    {
        $tests = [];
        
        // Add primary test
        if ($this->medicalTest) {
            $tests[] = [
                'category_id' => $this->medical_test_categories_id,
                'category_name' => $this->medicalTestCategory->name ?? 'Unknown',
                'test_id' => $this->medical_test_id,
                'test_name' => $this->medicalTest->name,
                'price' => $this->medicalTest->price ?? 0,
                'is_primary' => true,
            ];
        }

        // Add tests from other_exams if they exist (excluding the primary test to avoid duplicates)
        $parsedOtherExams = $this->parsed_other_exams;
        if ($parsedOtherExams && isset($parsedOtherExams['selected_tests'])) {
            foreach ($parsedOtherExams['selected_tests'] as $test) {
                // Skip if this is the same as the primary test (avoid duplicates)
                if (isset($test['test_id']) && $test['test_id'] == $this->medical_test_id) {
                    continue;
                }
                
                // If category_name is 'Unknown', try to fetch it from the database
                if (($test['category_name'] ?? '') === 'Unknown' && isset($test['category_id'])) {
                    $category = \App\Models\MedicalTestCategory::find($test['category_id']);
                    if ($category) {
                        $test['category_name'] = $category->name;
                    }
                }
                
                $tests[] = array_merge($test, ['is_primary' => false]);
            }
        }

        return collect($tests);
    }

    /**
     * Get drug test result for this pre-employment record
     */
    public function drugTestResult()
    {
        return $this->hasOne(\App\Models\DrugTestResult::class);
    }

    /**
     * Get pathologist-specific tests from selected tests
     * Expands pre-employment packages to show individual pathologist tests
     */
    public function getPathologistTestsAttribute()
    {
        $selectedTests = $this->all_selected_tests ?? collect();
        $pathologistTests = collect();
        
        // Collect all source information (packages and individual tests)
        $packageSources = [];
        $bloodChemistrySources = [];

        foreach ($selectedTests as $test) {
            // Check if this is a pre-employment package that needs expansion
            if ($this->isPreEmploymentPackage($test['test_name'])) {
                // Track package source
                $packageSources[] = [
                    'name' => $test['test_name'],
                    'price' => $test['price'],
                    'category' => $test['category_name'] ?? 'Medical Test Package'
                ];
                
                // Add the core pathologist tests for pre-employment packages
                $packageTests = $this->getPreEmploymentPackageTests($test['test_name']);
                foreach ($packageTests as $packageTest) {
                    $pathologistTests->push([
                        'test_name' => $packageTest['name'],
                        'category_name' => $packageTest['category'],
                        'price' => 0, // Individual test prices are included in package price
                        'is_package_component' => true,
                        'package_name' => $test['test_name'],
                        'package_price' => $test['price'],
                        'package_category' => $test['category_name'] ?? 'Medical Test Package',
                        'blood_chemistry_sources' => [], // Will be populated later
                    ]);
                }
                
                // Add blood chemistry tests for packages that include them
                if (str_contains(strtolower($test['test_name']), 'drug test')) {
                    $pathologistTests->push([
                        'test_name' => 'Blood Chemistry Panel',
                        'category_name' => 'Blood Chemistry',
                        'price' => 0,
                        'is_package_component' => true,
                        'package_name' => $test['test_name'],
                        'package_price' => $test['price'],
                        'package_category' => $test['category_name'] ?? 'Medical Test Package',
                        'blood_chemistry_sources' => [], // Will be populated later
                    ]);
                }
            } else {
                // Track Blood Chemistry sources separately
                if (($test['category_name'] ?? '') === 'Blood Chemistry') {
                    $bloodChemistrySources[] = [
                        'name' => $test['test_name'],
                        'price' => $test['price'] ?? 0,
                    ];
                }
                
                // For non-package tests, check if it's a pathologist test
                if ($this->isPathologistTest($test['test_name'], $test['category_name'])) {
                    $pathologistTests->push(array_merge($test, [
                        'is_package_component' => false,
                        'package_name' => null,
                        'package_price' => 0,
                        'package_category' => null,
                        'blood_chemistry_sources' => [],
                    ]));
                }
            }
        }
        
        // Add blood chemistry source information to all tests
        if (!empty($bloodChemistrySources)) {
            $pathologistTests = $pathologistTests->map(function($test) use ($bloodChemistrySources) {
                $test['blood_chemistry_sources'] = $bloodChemistrySources;
                return $test;
            });
        }

        return $pathologistTests;
    }

    /**
     * Check if a test is a pre-employment package
     */
    private function isPreEmploymentPackage($testName)
    {
        $preEmploymentPackages = [
            'Pre-Employment Medical Examination',
            'Pre-Employment with Drug Test',
            'Pre-Employment with ECG and Drug test',
            'Pre-Employment with Drug test and AUDIO and ISHIHARA',
        ];

        return is_string($testName) && in_array($testName, $preEmploymentPackages, true);
    }

    /**
     * Get pathologist tests for pre-employment packages
     */
    private function getPreEmploymentPackageTests($packageName)
    {
        // Core pathologist tests for all pre-employment packages
        $coreTests = [
            ['name' => 'CBC', 'category' => 'Routine Examinations'],
            ['name' => 'Fecalysis', 'category' => 'Routine Examinations'],
            ['name' => 'Urinalysis', 'category' => 'Clinical Microscopy'],
        ];

        // Note: Drug test, X-ray, Physical Exam, ECG, Audio, Ishihara are handled by other departments
        // Pathologist only handles the core lab tests (CBC, Fecalysis, Urinalysis)
        // Plus any Blood Chemistry tests if they're part of packages

        return $coreTests;
    }

    /**
     * Check if a test is handled by pathologist
     */
    private function isPathologistTest($testName, $categoryName)
    {
        $pathologistCategories = [
            'Routine Examinations',
            'Blood Chemistry',
            'Clinical Microscopy',
            'Serology',
            'Special hematology',
            'Histology',
            'Immunology',
            'Tumor Markers',
            'Thyroid Function Test',
            'Complete Hepa-Profile',
            'Bacteriology',
        ];

        // Ensure $categoryName is a string before checking in_array
        if (!is_string($categoryName)) {
            return false;
        }

        // Note: Drug Monitoring Assay is handled by medtech/nurse, not pathologist
        return in_array($categoryName, $pathologistCategories, true);
    }
}
