<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalTestCategory;
use App\Models\MedicalTest;

class MedicalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Pre-Employment',
                'description' => 'Pre-employment packages and options',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Appointment',
                'description' => 'Appointment packages',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Package',
                'description' => 'Package packages',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Routine Examinations',
                'description' => 'Basic routine medical examinations',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Histology',
                'description' => 'Histological examinations and tests',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Special Hematology',
                'description' => 'Specialized hematological tests',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Serology',
                'description' => 'Serological tests and analysis',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Clinical Microscopy',
                'description' => 'Clinical microscopy examinations',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Blood Chemistry',
                'description' => 'Blood chemistry and biochemical tests',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Immunology',
                'description' => 'Immunological tests and markers',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Tumor Markers',
                'description' => 'Tumor marker tests for cancer detection',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Thyroid Function Test',
                'description' => 'Thyroid function and hormone tests',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Drug Monitoring Assay',
                'description' => 'Drug testing and monitoring assays',
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'X-RAY',
                'description' => 'X-ray examinations and imaging',
                'sort_order' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Complete Hepa-Profile',
                'description' => 'Complete hepatitis profile tests',
                'sort_order' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Bacteriology',
                'description' => 'Bacteriological tests and cultures',
                'sort_order' => 16,
                'is_active' => true,
            ],
            [
                'name' => 'Liver Profile',
                'description' => 'Liver function tests and profiles',
                'sort_order' => 17,
                'is_active' => true,
            ],
            [
                'name' => 'Lipid Profile',
                'description' => 'Lipid and cholesterol profile tests',
                'sort_order' => 18,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = MedicalTestCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
            
            // Add tests for each category
            switch ($category->name) {
                case 'Pre-Employment':
                    $tests = [
                        ['name' => 'AUDIOMETRY AND ISHIHARA ONLY', 'description' => null, 'sort_order' => 0, 'price' => 500.00],
                        ['name' => 'Pre-Employment Medical Examination', 'description' => null, 'sort_order' => 1, 'price' => 600.00],
                        ['name' => 'Pre-Employment with Drug Test', 'description' => null, 'sort_order' => 2, 'price' => 750.00],
                        ['name' => 'Pre-Employment with ECG and Drug test', 'description' => null, 'sort_order' => 3, 'price' => 850.00],
                        ['name' => 'Pre-Employment with Drug test and AUDIO and ISHIHARA', 'description' => null, 'sort_order' => 4, 'price' => 800.00],
                        ['name' => 'Drug test only (bring valid I.D)', 'description' => null, 'sort_order' => 5, 'price' => 500.00],
                    ];
                    break;
                case 'Appointment':
                    $tests = [
                        ['name' => 'Annual Medical Examination', 'description' => 'Annual medical exam package', 'sort_order' => 1, 'price' => 600.00],
                        ['name' => 'Annual Medical with Drug Test', 'description' => 'Annual exam plus drug test', 'sort_order' => 2, 'price' => 750.00],
                        ['name' => 'Annual Medical with ECG and Drug test', 'description' => 'Annual exam plus ECG and drug test', 'sort_order' => 3, 'price' => 850.00],
                    ];
                    break;
                case 'Package':
                    $tests = [
                        // Package A through E have been removed as requested
                    ];
                    break;
                case 'Routine Examinations':
                    $tests = [
                        ['name' => 'CBC', 'description' => 'Complete Blood Count', 'sort_order' => 1, 'price' => 250.00],
                        ['name' => 'Urinalysis', 'description' => 'Urine Analysis', 'sort_order' => 2, 'price' => 150.00],
                        ['name' => 'Fecalysis', 'description' => 'Stool Analysis', 'sort_order' => 3, 'price' => 150.00],
                        ['name' => 'Pregnancy Test', 'description' => 'Pregnancy Test', 'sort_order' => 4, 'price' => 200.00],
                    ];
                    break;
                    
                case 'Histology':
                    $tests = [
                        ['name' => 'Pap\'s reading only', 'description' => 'Pap smear reading only', 'sort_order' => 1, 'price' => 300.00],
                        ['name' => 'Papsmear', 'description' => 'Complete Pap smear test', 'sort_order' => 2, 'price' => 300.00],
                    ];
                    break;
                    
                case 'Special Hematology':
                    $tests = [
                        ['name' => 'Platelet Count', 'description' => 'Platelet count test', 'sort_order' => 1, 'price' => 250.00],
                        ['name' => 'Protime', 'description' => 'Prothrombin time test', 'sort_order' => 2, 'price' => 450.00],
                        ['name' => 'APTT', 'description' => 'Activated Partial Thromboplastin Time', 'sort_order' => 3, 'price' => 450.00],
                        ['name' => 'ESR', 'description' => 'Erythrocyte Sedimentation Rate', 'sort_order' => 4, 'price' => 250.00],
                        ['name' => 'ABO Typing w/ RH', 'description' => 'Blood type and Rh factor', 'sort_order' => 5, 'price' => 400.00],
                        ['name' => 'L.E. Prep', 'description' => 'Lupus Erythematosus preparation', 'sort_order' => 6, 'price' => 400.00],
                        ['name' => 'Malarial Smear', 'description' => 'Malaria detection test', 'sort_order' => 7, 'price' => 400.00],
                        ['name' => 'Retic CT', 'description' => 'Reticulocyte count', 'sort_order' => 8, 'price' => 350.00],
                        ['name' => 'Peripheral Smear', 'description' => 'Peripheral blood smear', 'sort_order' => 9, 'price' => 200.00],
                        ['name' => 'Clotting & Bleeding Time', 'description' => 'Clotting and bleeding time test', 'sort_order' => 10, 'price' => 200.00],
                    ];
                    break;
                    
                case 'Serology':
                    $tests = [
                        ['name' => 'VDRL', 'description' => 'Venereal Disease Research Laboratory test', 'sort_order' => 1, 'price' => 350.00],
                        ['name' => 'ASO Titer', 'description' => 'Anti-Streptolysin O titer', 'sort_order' => 2, 'price' => 450.00],
                        ['name' => 'RA Factor', 'description' => 'Rheumatoid factor test', 'sort_order' => 3, 'price' => 450.00],
                        ['name' => 'Typhidot', 'description' => 'Typhoid fever test', 'sort_order' => 4, 'price' => 800.00],
                        ['name' => 'STAT FEE', 'description' => 'Stat fee for urgent tests', 'sort_order' => 5, 'price' => 100.00],
                    ];
                    break;
                    
                case 'Clinical Microscopy':
                    $tests = [
                        ['name' => 'Occult Blood', 'description' => 'Occult blood test', 'sort_order' => 1, 'price' => 350.00],
                    ];
                    break;
                    
                case 'Blood Chemistry':
                    $tests = [
                        ['name' => 'Package A', 'description' => 'ELECTROCARDIOGRAM, FASTING BLOOD SUGAR, TOTAL CHOLESTEROL, BLOOD UREA NITROGEN, BLOOD URIC ACID, SGPT, CBC, URINALYSIS AND FECALYSIS', 'sort_order' => 1, 'price' => 1600.00],
                        ['name' => 'Package B', 'description' => 'PACKAGE A PLUS TRIGLYCERIDE, HDL & LDL (GOOD AND BAD CHOLESTEROL)', 'sort_order' => 2, 'price' => 1800.00],
                        ['name' => 'Package C', 'description' => 'PACKAGE A B PLUS CREATININE & BLD. TYPING', 'sort_order' => 3, 'price' => 2050.00],
                        ['name' => 'Package D', 'description' => 'PACKAGE A B C PLUS SODIUM, POTASSIUM, CALCIUM, CHLORIDE', 'sort_order' => 4, 'price' => 2200.00],
                        ['name' => 'Package E', 'description' => 'PACKAGE A B C D PLUS (LIVER FUNCTION TEST) TPAG, SGOT, BILIRUBIN PLUS AMYLASE', 'sort_order' => 5, 'price' => 2800.00],
                        ['name' => 'Chloride', 'description' => 'Chloride test', 'sort_order' => 6, 'price' => 350.00],
                        ['name' => 'Inor. Phosp', 'description' => 'Inorganic Phosphorus', 'sort_order' => 7, 'price' => 400.00],
                        ['name' => 'Magnesium', 'description' => 'Magnesium test', 'sort_order' => 8, 'price' => 400.00],
                        ['name' => 'HBA1C', 'description' => 'Hemoglobin A1C', 'sort_order' => 9, 'price' => 500.00],
                        ['name' => 'SGOT/ AST', 'description' => 'Serum Glutamic Oxaloacetic Transaminase / Aspartate Aminotransferase', 'sort_order' => 10, 'price' => 350.00],
                        ['name' => 'SGPT/ ALT', 'description' => 'Serum Glutamic Pyruvic Transaminase / Alanine Aminotransferase', 'sort_order' => 11, 'price' => 350.00],
                        ['name' => 'Alk. Phosp', 'description' => 'Alkaline Phosphatase', 'sort_order' => 12, 'price' => 400.00],
                        ['name' => 'Acid. Phosp', 'description' => 'Acid Phosphatase', 'sort_order' => 13, 'price' => 350.00],
                        ['name' => 'Amylase', 'description' => 'Amylase test', 'sort_order' => 14, 'price' => 450.00],
                        ['name' => 'OGCT', 'description' => 'Oral Glucose Challenge Test', 'sort_order' => 15, 'price' => 700.00],
                        ['name' => 'Additional 80.00 per cup', 'description' => 'Additional charge per cup for OGCT', 'sort_order' => 16, 'price' => 80.00],
                        ['name' => 'OGTT', 'description' => 'Oral Glucose Tolerance Test', 'sort_order' => 17, 'price' => 1000.00],
                        ['name' => 'Additional 80.00 per cup', 'description' => 'Additional charge per cup for OGTT', 'sort_order' => 18, 'price' => 80.00],
                        ['name' => 'FBS', 'description' => 'Fasting Blood Sugar', 'sort_order' => 19, 'price' => 250.00],
                        ['name' => 'BUA', 'description' => 'Blood Uric Acid', 'sort_order' => 20, 'price' => 250.00],
                        ['name' => 'BUN', 'description' => 'Blood Urea Nitrogen', 'sort_order' => 21, 'price' => 250.00],
                        ['name' => 'Cholesterol', 'description' => 'Cholesterol test', 'sort_order' => 22, 'price' => 250.00],
                        ['name' => 'Creatinine', 'description' => 'Creatinine test', 'sort_order' => 23, 'price' => 300.00],
                        ['name' => 'Triglycerides', 'description' => 'Triglycerides test', 'sort_order' => 24, 'price' => 300.00],
                        ['name' => 'HDL & LDL', 'description' => 'High and Low Density Lipoproteins', 'sort_order' => 25, 'price' => 400.00],
                        ['name' => 'VLDL', 'description' => 'Very Low Density Lipoprotein', 'sort_order' => 26, 'price' => 350.00],
                        ['name' => 'SGOT', 'description' => 'Serum Glutamic Oxaloacetic Transaminase', 'sort_order' => 27, 'price' => 350.00],
                        ['name' => 'SGPT', 'description' => 'Serum Glutamic Pyruvic Transaminase', 'sort_order' => 28, 'price' => 350.00],
                        ['name' => 'Bilirubin', 'description' => 'Bilirubin test', 'sort_order' => 29, 'price' => 450.00],
                        ['name' => 'TPAG', 'description' => 'Total Protein, Albumin, Globulin', 'sort_order' => 30, 'price' => 450.00],
                        ['name' => 'A/G Ratio', 'description' => 'Albumin/Globulin ratio', 'sort_order' => 31, 'price' => 350.00],
                        ['name' => 'Sodium', 'description' => 'Sodium test', 'sort_order' => 32, 'price' => 350.00],
                        ['name' => 'Potassium', 'description' => 'Potassium test', 'sort_order' => 33, 'price' => 350.00],
                        ['name' => 'Calcium', 'description' => 'Calcium test', 'sort_order' => 34, 'price' => 350.00],
                        ['name' => 'Ionized Calcium', 'description' => 'Ionized calcium test', 'sort_order' => 35, 'price' => 450.00],
                    ];
                    break;
                    
                case 'Immunology':
                    $tests = [
                        ['name' => 'PSA', 'description' => 'Prostate Specific Antigen', 'sort_order' => 1, 'price' => 1000.00],
                    ];
                    break;
                    
                case 'Tumor Markers':
                    $tests = [
                        ['name' => 'Alpha Feto Protein', 'description' => 'Alpha Fetoprotein tumor marker', 'sort_order' => 1, 'price' => 500.00],
                        ['name' => 'CEA', 'description' => 'Carcinoembryonic Antigen', 'sort_order' => 2, 'price' => 700.00],
                        ['name' => 'B-HCG Serum', 'description' => 'Beta Human Chorionic Gonadotropin', 'sort_order' => 3, 'price' => 550.00],
                        ['name' => 'CA-125', 'description' => 'Cancer Antigen 125', 'sort_order' => 4, 'price' => 1500.00],
                        ['name' => 'CA-153', 'description' => 'Cancer Antigen 15-3', 'sort_order' => 5, 'price' => 1500.00],
                        ['name' => 'CA-199', 'description' => 'Cancer Antigen 19-9', 'sort_order' => 6, 'price' => 1700.00],
                        ['name' => 'CA-72-3', 'description' => 'Cancer Antigen 72-3', 'sort_order' => 7, 'price' => 1800.00],
                    ];
                    break;
                    
                case 'Thyroid Function Test':
                    $tests = [
                        ['name' => 'T3', 'description' => 'Triiodothyronine', 'sort_order' => 1, 'price' => 500.00],
                        ['name' => 'T4', 'description' => 'Thyroxine', 'sort_order' => 2, 'price' => 400.00],
                        ['name' => 'TSH', 'description' => 'Thyroid Stimulating Hormone', 'sort_order' => 3, 'price' => 500.00],
                        ['name' => 'FT3', 'description' => 'Free Triiodothyronine', 'sort_order' => 4, 'price' => 500.00],
                        ['name' => 'FT4', 'description' => 'Free Thyroxine', 'sort_order' => 5, 'price' => 500.00],
                    ];
                    break;
                    
                case 'Drug Monitoring Assay':
                    $tests = [
                        ['name' => 'Drugtest (Met & THC)', 'description' => 'Drug test for Methamphetamine and THC', 'sort_order' => 1, 'price' => 350.00],
                    ];
                    break;
                    
                case 'X-RAY':
                    $tests = [
                        ['name' => 'CXR-PA 11X14', 'description' => 'Chest X-ray Posteroanterior 11x14', 'sort_order' => 1, 'price' => 400.00],
                        ['name' => 'Apico Lordotic View', 'description' => 'Apical lordotic chest X-ray view', 'sort_order' => 2, 'price' => 500.00],
                        ['name' => 'AP-Lateral Adult', 'description' => 'Anteroposterior and Lateral adult X-ray', 'sort_order' => 3, 'price' => 500.00],
                        ['name' => 'AP-Lateral Child', 'description' => 'Anteroposterior and Lateral child X-ray', 'sort_order' => 4, 'price' => 1000.00],
                        ['name' => 'Electrocardiogram', 'description' => 'ECG/EKG test', 'sort_order' => 5, 'price' => 400.00],
                        ['name' => 'Chest X-ray with Package', 'description' => 'Additional 300.00 for package with chest X-ray', 'sort_order' => 6, 'price' => 300.00],
                    ];
                    break;
                    
                case 'Complete Hepa-Profile':
                    $tests = [
                        ['name' => 'Hepa B Profile', 'description' => 'Hepatitis B profile test', 'sort_order' => 1, 'price' => 1500.00],
                        ['name' => 'Hepa A & B Profile', 'description' => 'Hepatitis A and B profile test', 'sort_order' => 2, 'price' => 1800.00],
                        ['name' => 'Hepa ABC & Profile', 'description' => 'Hepatitis A, B, and C profile test', 'sort_order' => 3, 'price' => 2000.00],
                        ['name' => 'HBsAg (Latex)', 'description' => 'Hepatitis B Surface Antigen (Latex)', 'sort_order' => 4, 'price' => 400.00],
                        ['name' => 'Elisa', 'description' => 'Enzyme-Linked Immunosorbent Assay', 'sort_order' => 5, 'price' => 500.00],
                        ['name' => 'Anti-Hbs', 'description' => 'Anti-Hepatitis B Surface antibody', 'sort_order' => 6, 'price' => 500.00],
                        ['name' => 'Anti-Hbc lgG', 'description' => 'Anti-Hepatitis B Core IgG', 'sort_order' => 7, 'price' => 500.00],
                        ['name' => 'Anti-Hbc lGM', 'description' => 'Anti-Hepatitis B Core IgM', 'sort_order' => 8, 'price' => 500.00],
                        ['name' => 'Anti-Hav lgG', 'description' => 'Anti-Hepatitis A IgG', 'sort_order' => 9, 'price' => 500.00],
                        ['name' => 'Anti-Hav lGM', 'description' => 'Anti-Hepatitis A IgM', 'sort_order' => 10, 'price' => 500.00],
                        ['name' => 'HBeAg', 'description' => 'Hepatitis B e Antigen', 'sort_order' => 11, 'price' => 500.00],
                        ['name' => 'Anti-Hbe', 'description' => 'Anti-Hepatitis B e antibody', 'sort_order' => 12, 'price' => 500.00],
                        ['name' => 'Anti-Hcv', 'description' => 'Anti-Hepatitis C antibody', 'sort_order' => 13, 'price' => 650.00],
                    ];
                    break;
                    
                case 'Bacteriology':
                    $tests = [
                        ['name' => 'All culture & Sensitivity', 'description' => 'All culture and sensitivity tests', 'sort_order' => 1, 'price' => 1500.00],
                        ['name' => 'KOH Mount', 'description' => 'Potassium Hydroxide mount test', 'sort_order' => 2, 'price' => 350.00],
                        ['name' => 'Gram Stain', 'description' => 'Gram staining test', 'sort_order' => 3, 'price' => 350.00],
                        ['name' => 'AFB', 'description' => 'Acid-Fast Bacilli test', 'sort_order' => 4, 'price' => 300.00],
                        ['name' => 'Blood C/S', 'description' => 'Blood Culture and Sensitivity', 'sort_order' => 5, 'price' => 600.00],
                    ];
                    break;
                    
                case 'Liver Profile':
                    $tests = [
                        ['name' => 'Liver Profile', 'description' => 'Complete liver function profile', 'sort_order' => 1, 'price' => 1350.00],
                        ['name' => 'Bilirubin', 'description' => 'Bilirubin test', 'sort_order' => 2, 'price' => 450.00],
                        ['name' => 'Alk. Phos', 'description' => 'Alkaline Phosphatase', 'sort_order' => 3, 'price' => 400.00],
                        ['name' => 'SGOT', 'description' => 'Serum Glutamic Oxaloacetic Transaminase', 'sort_order' => 4, 'price' => 350.00],
                        ['name' => 'SGPT', 'description' => 'Serum Glutamic Pyruvic Transaminase', 'sort_order' => 5, 'price' => 350.00],
                        ['name' => 'TPAG', 'description' => 'Total Protein, Albumin, Globulin', 'sort_order' => 6, 'price' => 450.00],
                    ];
                    break;
                    
                case 'Lipid Profile':
                    $tests = [
                        ['name' => 'Lipid Profile', 'description' => 'Complete lipid profile', 'sort_order' => 1, 'price' => 800.00],
                        ['name' => 'T. Cholesterol', 'description' => 'Total Cholesterol', 'sort_order' => 2, 'price' => 250.00],
                        ['name' => 'Triglycerides', 'description' => 'Triglycerides test', 'sort_order' => 3, 'price' => 300.00],
                        ['name' => 'HDL / LDL / VLDL', 'description' => 'High, Low, and Very Low Density Lipoproteins', 'sort_order' => 4, 'price' => 400.00],
                    ];
                    break;
                    
                default:
                    $tests = [];
            }
            
            foreach ($tests as $testData) {
                MedicalTest::updateOrCreate(
                    [
                        'medical_test_category_id' => $category->id,
                        'name' => $testData['name']
                    ],
                    [
                        'description' => $testData['description'],
                        'sort_order' => $testData['sort_order'],
                        'price' => $testData['price'] ?? 0,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
