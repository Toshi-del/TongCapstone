<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'age',
        'sex',
        'email',
        'phone',
        'address',
        'appointment_id',
        'company_name',
        'status',
        'medical_test_id',
        'age_adjusted',
        'original_test_name',
        'adjusted_test_name',
    ];

    public function appointment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medicalTest(): BelongsTo
    {
        return $this->belongsTo(MedicalTest::class);
    }

    /**
     * Many-to-many relationship with medical tests
     */
    public function medicalTests(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MedicalTest::class, 'patient_medical_tests');
    }

    /**
     * Get all medical tests for this patient (fallback to appointment if pivot table is empty)
     */
    public function getAllMedicalTests()
    {
        // First try to get from pivot table
        $pivotTests = $this->medicalTests;
        
        if ($pivotTests->isNotEmpty()) {
            return $pivotTests;
        }
        
        // Fallback to appointment relationship
        if ($this->appointment) {
            return $this->appointment->selected_tests;
        }
        
        return collect();
    }

    /**
     * Get medical test names as a comma-separated string
     */
    public function getMedicalTestNamesAttribute(): string
    {
        $tests = $this->getAllMedicalTests();
        return $tests->pluck('name')->implode(', ');
    }

    /**
     * Get medical test IDs as an array
     */
    public function getMedicalTestIdsAttribute(): array
    {
        $tests = $this->getAllMedicalTests();
        return $tests->pluck('id')->toArray();
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name . ' ' . $this->last_name;
        if ($this->middle_name) {
            $name = $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
        }
        return $name;
    }

    public function getAgeSexAttribute(): string
    {
        return $this->age . ' / ' . $this->sex;
    }

    public function annualPhysicalExamination(): HasOne
    {
        return $this->hasOne(AnnualPhysicalExamination::class);
    }

    public function medicalChecklist(): HasOne
    {
        return $this->hasOne(MedicalChecklist::class, 'patient_id');
    }

    /**
     * Get all medical checklists for this patient (for filtering)
     */
    public function medicalChecklists(): HasMany
    {
        return $this->hasMany(MedicalChecklist::class, 'patient_id');
    }

    /**
     * Get pathologist-specific tests from appointment's selected tests
     * Similar to PreEmploymentRecord but for annual physical appointments
     */
    public function getPathologistTestsAttribute()
    {
        if (!$this->appointment) {
            return collect();
        }

        $selectedTests = $this->appointment->selected_tests ?? collect();
        $pathologistTests = collect();
        
        // Collect all source information (packages and individual tests)
        $packageSources = [];
        $bloodChemistrySources = [];

        foreach ($selectedTests as $test) {
            // Check if this is an annual physical package that needs expansion
            if ($this->isAnnualPhysicalPackage($test->name)) {
                // Track package source
                $packageSources[] = [
                    'name' => $test->name,
                    'price' => $test->price,
                    'category' => $test->category->name ?? 'Medical Test Package'
                ];
                
                // Add the core pathologist tests for annual physical packages
                $packageTests = $this->getAnnualPhysicalPackageTests($test->name);
                foreach ($packageTests as $packageTest) {
                    $pathologistTests->push([
                        'test_name' => $packageTest['name'],
                        'category_name' => $packageTest['category'],
                        'price' => 0, // Individual test prices are included in package price
                        'is_package_component' => true,
                        'package_name' => $test->name,
                        'package_price' => $test->price,
                        'package_category' => $test->category->name ?? 'Medical Test Package',
                        'blood_chemistry_sources' => [], // Will be populated later
                    ]);
                }
            } else {
                // Track Blood Chemistry sources separately
                if (($test->category->name ?? '') === 'Blood Chemistry') {
                    $bloodChemistrySources[] = [
                        'name' => $test->name,
                        'price' => $test->price ?? 0,
                    ];
                }
                
                // For non-package tests, check if it's a pathologist test
                if ($this->isPathologistTest($test->name, $test->category->name ?? '')) {
                    $pathologistTests->push([
                        'test_name' => $test->name,
                        'category_name' => $test->category->name ?? 'Laboratory Test',
                        'price' => $test->price ?? 0,
                        'is_package_component' => false,
                        'package_name' => null,
                        'package_price' => 0,
                        'package_category' => null,
                        'blood_chemistry_sources' => [], // Will be populated later
                    ]);
                }
            }
        }

        // Add blood chemistry source information to all tests
        $pathologistTests = $pathologistTests->map(function($test) use ($bloodChemistrySources) {
            $test['blood_chemistry_sources'] = $bloodChemistrySources;
            return $test;
        });

        return $pathologistTests;
    }

    /**
     * Check if a test is an annual physical package
     */
    private function isAnnualPhysicalPackage($testName)
    {
        $annualPhysicalPackages = [
            'Annual Medical Examination',
            'Annual Medical with Drug Test',
            'Annual Medical with Drug Test and ECG',
            'Annual Medical Examination with Drug Test',
            'Annual Medical Examination with Drug Test and ECG',
        ];

        return in_array($testName, $annualPhysicalPackages);
    }

    /**
     * Get pathologist tests included in annual physical packages
     */
    private function getAnnualPhysicalPackageTests($packageName)
    {
        // Core pathologist tests for annual physical packages
        $coreTests = [
            ['name' => 'Complete Blood Count (CBC)', 'category' => 'Hematology'],
            ['name' => 'Urinalysis', 'category' => 'Clinical Pathology'],
            ['name' => 'Stool Examination', 'category' => 'Clinical Pathology'],
        ];

        // Additional tests based on package type
        $additionalTests = [];
        
        if (strpos($packageName, 'Drug Test') !== false) {
            // Drug test packages don't typically add extra pathologist tests
            // Drug tests are handled by medical technologists
        }

        return array_merge($coreTests, $additionalTests);
    }

    /**
     * Check if a test requires pathologist attention
     */
    private function isPathologistTest($testName, $categoryName)
    {
        // Pathologist categories
        $pathologistCategories = [
            'Hematology',
            'Clinical Pathology', 
            'Blood Chemistry',
            'Serology',
            'Microbiology',
            'Parasitology',
        ];

        // Specific pathologist tests
        $pathologistTests = [
            'Complete Blood Count (CBC)',
            'Urinalysis',
            'Stool Examination',
            'Fasting Blood Sugar (FBS)',
            'Blood Urea Nitrogen (BUN)',
            'Creatinine',
            'SGPT/ALT',
            'SGOT/AST',
            'Total Cholesterol',
            'Triglycerides',
            'HDL Cholesterol',
            'LDL Cholesterol',
            'Uric Acid',
            'HbA1c',
            'Hepatitis B Surface Antigen',
            'VDRL/RPR',
            'Pregnancy Test',
        ];

        return in_array($categoryName, $pathologistCategories) || 
               in_array($testName, $pathologistTests);
    }
}
