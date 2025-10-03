<?php $__env->startSection('title', 'Create New Appointment - RSS Citi Health Services'); ?>
<?php $__env->startSection('page-title', 'Create New Appointment'); ?>
<?php $__env->startSection('page-description', 'Schedule a new medical examination appointment for your employees'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-blue-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-calendar-plus text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Schedule New Appointment</h2>
                        <p class="text-blue-100 text-sm">Create a medical examination appointment with patient data</p>
                    </div>
                </div>
                <a href="<?php echo e(route('company.appointments.index')); ?>" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 backdrop-blur-sm border border-white/20 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Calendar
                </a>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    <?php if(session('error')): ?>
    <div class="content-card rounded-xl p-4 shadow-lg border border-red-200 bg-red-50">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-red-800 font-medium"><?php echo e(session('error')); ?></p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="content-card rounded-xl p-4 shadow-lg border border-red-200 bg-red-50">
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-2">Please correct the following errors:</h3>
                <ul class="text-sm text-red-700 space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-circle text-xs text-red-500"></i>
                            <span><?php echo e($error); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Appointment Details -->
            <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Appointment Details</h3>
                        <p class="text-gray-600 text-sm">Select date and time for the medical examination</p>
                    </div>
                </div>

                <form action="<?php echo e(route('company.appointments.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Appointment Date -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Appointment Date <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" 
                                       name="appointment_date" 
                                       value="<?php echo e(old('appointment_date', request('date'))); ?>" 
                                       min="<?php echo e($minDate); ?>"
                                       id="appointment_date"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                            </div>
                            <div id="date-availability-message" class="text-sm" style="display: none;"></div>
                            <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                            </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Time Slot -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Time Slot <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="time_slot" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['time_slot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        required>
                                    <option value="">Choose available time</option>
                                    <?php $__currentLoopData = $timeSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($slot); ?>" <?php echo e(old('time_slot') == $slot ? 'selected' : ''); ?>><?php echo e($slot); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-clock text-gray-400"></i>
                                </div>
                            </div>
                            <?php $__errorArgs = ['time_slot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                            </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Scheduling Rules Info -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 mb-2">Scheduling Guidelines</h4>
                                <ul class="text-sm text-blue-700 space-y-1">
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-blue-500 text-xs"></i>
                                        <span>Appointments must be scheduled at least 4 days in advance</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-blue-500 text-xs"></i>
                                        <span>Only one company can book per day</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-blue-500 text-xs"></i>
                                        <span>No Sunday appointments (Saturdays available)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

            </div>

            <!-- Medical Tests Selection -->
            <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-vials text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Medical Tests</h3>
                        <p class="text-gray-600 text-sm">Choose the medical examination package or individual tests</p>
                    </div>
                </div>

                <input type="hidden" name="medical_test_categories_id" id="medical_test_categories_id" value="<?php echo e(is_array(old('medical_test_categories_id')) ? json_encode(old('medical_test_categories_id')) : old('medical_test_categories_id')); ?>">
                <input type="hidden" name="medical_test_id" id="medical_test_id" value="<?php echo e(is_array(old('medical_test_id')) ? json_encode(old('medical_test_id')) : old('medical_test_id')); ?>">
                <div id="selected_tests_container">
                    <!-- Selected tests will be added here as hidden inputs -->
                </div>
                <input type="hidden" name="total_price" id="total_price" value="0">
                <?php $__errorArgs = ['medical_test_categories_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['medical_test_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <?php
                    $uniqueCategories = $medicalTestCategories->unique(function($c){ return strtolower($c->name ?? ''); });
                ?>
                <?php $__currentLoopData = $uniqueCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                        $categoryName = strtolower(trim($category->name)); 
                        $uniqueTests = $category->medicalTests->unique(function($t){ return strtolower($t->name ?? ''); });
                    ?>
                    <?php if($categoryName === 'appointment' || $categoryName === 'blood chemistry'): ?>
                        <div class="mb-6 last:mb-0">
                            <!-- Collapsible Category Header -->
                            <div class="bg-white rounded-lg border-l-4 border-indigo-600 overflow-hidden">
                                <button type="button" 
                                        class="w-full p-4 text-left hover:bg-indigo-50 transition-colors duration-200 focus:outline-none focus:bg-indigo-50"
                                        onclick="toggleCategory('category-<?php echo e($category->id); ?>')">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-xl font-bold text-indigo-900 mb-1">
                                                <i class="fas fa-vial mr-2"></i><?php echo e($category->name); ?>

                                            </h4>
                                            <?php if($category->description): ?>
                                                <p class="text-sm text-indigo-700"><?php echo e($category->description); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span id="selected-count-<?php echo e($category->id); ?>" class="text-sm font-medium text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full hidden">
                                                0 selected
                                            </span>
                                            <i id="chevron-<?php echo e($category->id); ?>" class="fas fa-chevron-down text-indigo-600 transform transition-transform duration-200"></i>
                                        </div>
                                    </div>
                                </button>
                                
                                <!-- Collapsible Content -->
                                <div id="category-<?php echo e($category->id); ?>" class="hidden border-t border-indigo-100">
                                    <div class="p-4 bg-indigo-50">
                                        <!-- Blocking Notice -->
                                        <div id="blocking-notice-<?php echo e($category->id); ?>" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-info-circle text-yellow-600"></i>
                                                <span class="text-sm text-yellow-800 font-medium" id="blocking-text-<?php echo e($category->id); ?>"></span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <?php $__currentLoopData = $uniqueTests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    // Skip package-type tests in Blood Chemistry category
                                                    $isPackageTest = str_contains(strtolower($test->name), 'package');
                                                    if($categoryName === 'blood chemistry' && $isPackageTest) {
                                                        continue;
                                                    }
                                                ?>
                                                <label for="appointment_test_<?php echo e($test->id); ?>" class="cursor-pointer block h-full">
                                                    <div class="bg-white rounded-lg p-5 border-2 border-gray-200 hover:border-blue-400 hover:shadow-md transition-all duration-200 h-full flex flex-col">
                                                        <div class="flex items-start flex-1">
                                                            <input
                                                                id="appointment_test_<?php echo e($test->id); ?>"
                                                                type="checkbox"
                                                                name="appointment_selected_test"
                                                                value="<?php echo e($test->id); ?>"
                                                                data-category-id="<?php echo e($category->id); ?>"
                                                                data-test-id="<?php echo e($test->id); ?>"
                                                                class="mt-1 h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded category-checkbox"
                                                            >
                                                            <div class="ml-3 flex-1 flex flex-col justify-between min-h-0">
                                                                <div class="flex-1">
                                                                    <h5 class="text-base font-bold text-gray-900 mb-1"><?php echo e($test->name); ?></h5>
                                                                    <?php if($test->description): ?>
                                                                        <?php
                                                                            $isPackageTest = str_contains(strtolower($test->name), 'package');
                                                                            $description = $test->description;
                                                                            
                                                                            // For package tests, show full description and format as list if it contains separators
                                                                            if ($isPackageTest) {
                                                                                // Expand hierarchical packages to show complete test lists
                                                                                $testName = strtolower(trim($test->name));
                                                                                
                                                                                // Define the complete test lists for each package
                                                                                $packageTests = [
                                                                                    'package a' => [
                                                                                        'ELECTROCARDIOGRAM',
                                                                                        'FASTING BLOOD SUGAR', 
                                                                                        'TOTAL CHOLESTEROL',
                                                                                        'BLOOD UREA NITROGEN',
                                                                                        'BLOOD URIC ACID',
                                                                                        'SGPT',
                                                                                        'CBC',
                                                                                        'URINALYSIS',
                                                                                        'FECALYSIS'
                                                                                    ],
                                                                                    'package b' => [
                                                                                        'ELECTROCARDIOGRAM',
                                                                                        'FASTING BLOOD SUGAR', 
                                                                                        'TOTAL CHOLESTEROL',
                                                                                        'BLOOD UREA NITROGEN',
                                                                                        'BLOOD URIC ACID',
                                                                                        'SGPT',
                                                                                        'CBC',
                                                                                        'URINALYSIS',
                                                                                        'FECALYSIS',
                                                                                        'TRIGLYCERIDE',
                                                                                        'HDL & LDL (GOOD AND BAD CHOLESTEROL)'
                                                                                    ],
                                                                                    'package c' => [
                                                                                        'ELECTROCARDIOGRAM',
                                                                                        'FASTING BLOOD SUGAR', 
                                                                                        'TOTAL CHOLESTEROL',
                                                                                        'BLOOD UREA NITROGEN',
                                                                                        'BLOOD URIC ACID',
                                                                                        'SGPT',
                                                                                        'CBC',
                                                                                        'URINALYSIS',
                                                                                        'FECALYSIS',
                                                                                        'TRIGLYCERIDE',
                                                                                        'HDL & LDL (GOOD AND BAD CHOLESTEROL)',
                                                                                        'CREATININE',
                                                                                        'BLOOD TYPING'
                                                                                    ],
                                                                                    'package d' => [
                                                                                        'ELECTROCARDIOGRAM',
                                                                                        'FASTING BLOOD SUGAR', 
                                                                                        'TOTAL CHOLESTEROL',
                                                                                        'BLOOD UREA NITROGEN',
                                                                                        'BLOOD URIC ACID',
                                                                                        'SGPT',
                                                                                        'CBC',
                                                                                        'URINALYSIS',
                                                                                        'FECALYSIS',
                                                                                        'TRIGLYCERIDE',
                                                                                        'HDL & LDL (GOOD AND BAD CHOLESTEROL)',
                                                                                        'CREATININE',
                                                                                        'BLOOD TYPING',
                                                                                        'SODIUM',
                                                                                        'POTASSIUM',
                                                                                        'CALCIUM',
                                                                                        'CHLORIDE'
                                                                                    ],
                                                                                    'package e' => [
                                                                                        'ELECTROCARDIOGRAM',
                                                                                        'FASTING BLOOD SUGAR', 
                                                                                        'TOTAL CHOLESTEROL',
                                                                                        'BLOOD UREA NITROGEN',
                                                                                        'BLOOD URIC ACID',
                                                                                        'SGPT',
                                                                                        'CBC',
                                                                                        'URINALYSIS',
                                                                                        'FECALYSIS',
                                                                                        'TRIGLYCERIDE',
                                                                                        'HDL & LDL (GOOD AND BAD CHOLESTEROL)',
                                                                                        'CREATININE',
                                                                                        'BLOOD TYPING',
                                                                                        'SODIUM',
                                                                                        'POTASSIUM',
                                                                                        'CALCIUM',
                                                                                        'CHLORIDE',
                                                                                        'TPAG (LIVER FUNCTION TEST)',
                                                                                        'SGOT',
                                                                                        'BILIRUBIN',
                                                                                        'AMYLASE'
                                                                                    ]
                                                                                ];
                                                                                
                                                                                // Use expanded list if available, otherwise fall back to original parsing
                                                                                if (isset($packageTests[$testName])) {
                                                                                    $hasMultipleItems = true;
                                                                                    $expandedTests = $packageTests[$testName];
                                                                                } else {
                                                                                    // Check for common separators and convert to list
                                                                                    $separators = [',', ';', '|', ' + ', ' & ', ' and '];
                                                                                    $hasMultipleItems = false;
                                                                                    $separator = '';
                                                                                    
                                                                                    foreach ($separators as $sep) {
                                                                                        if (str_contains($description, $sep)) {
                                                                                            $hasMultipleItems = true;
                                                                                            $separator = $sep;
                                                                                            break;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        ?>
                                                                        
                                                                        <?php if($isPackageTest && isset($hasMultipleItems) && $hasMultipleItems): ?>
                                                                            <div class="text-sm text-gray-600 mb-2">
                                                                                <p class="font-medium mb-1">Package includes:</p>
                                                                                <ul class="list-disc list-inside space-y-1 ml-2">
                                                                                    <?php if(isset($expandedTests)): ?>
                                                                                        <?php $__currentLoopData = $expandedTests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                            <li class="text-xs"><?php echo e($item); ?></li>
                                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                    <?php else: ?>
                                                                                        <?php $__currentLoopData = array_map('trim', explode($separator, $description)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                            <?php if(!empty($item)): ?>
                                                                                                <li class="text-xs"><?php echo e($item); ?></li>
                                                                                            <?php endif; ?>
                                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                    <?php endif; ?>
                                                                                </ul>
                                                                            </div>
                                                                        <?php elseif($isPackageTest): ?>
                                                                            <p class="text-sm text-gray-600 mb-2"><?php echo e($description); ?></p>
                                                                        <?php else: ?>
                                                                            <p class="text-sm text-gray-600 mb-2"><?php echo e(Str::limit($description, 60)); ?></p>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="mt-auto pt-2">
                                                                    <?php if(!is_null($test->price) && $test->price > 0): ?>
                                                                        <div class="bg-emerald-50 rounded-lg px-3 py-1 inline-block">
                                                                            <p class="text-sm font-bold text-emerald-700">₱<?php echo e(number_format((float)$test->price, 2)); ?></p>
                                                                        </div>
                                                                    <?php elseif(!is_null($test->price) && $test->price == 0): ?>
                                                                        <div class="bg-blue-50 rounded-lg px-3 py-1 inline-block">
                                                                            <p class="text-sm font-bold text-blue-700">Contact for pricing</p>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Additional Information -->
            <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Additional Information</h3>
                        <p class="text-gray-600 text-sm">Notes and patient data upload</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Notes -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Additional Notes
                        </label>
                        <textarea name="notes" 
                                  rows="4" 
                                  placeholder="Enter any special instructions, requirements, or notes for this appointment..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                        </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Excel File Upload -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Patient Data (Excel File)
                        </label>
                        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200 mb-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-info-circle text-emerald-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-emerald-800 mb-1">Optional Upload</h4>
                                    <p class="text-sm text-emerald-700">Upload an Excel file with patient data. Required columns: First Name, Last Name, Age, Sex, Email, Phone, Address.</p>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="file" 
                                   name="excel_file" 
                                   accept=".xlsx,.xls"
                                   id="excel_file"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 <?php $__errorArgs = ['excel_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-file-excel text-gray-400"></i>
                            </div>
                        </div>
                        <?php $__errorArgs = ['excel_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                        </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <!-- File Preview Section -->
                        <div id="filePreview" class="mt-4 hidden">
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-file-excel text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-blue-800">File Preview</h4>
                                        <p id="fileName" class="text-sm text-blue-700"></p>
                                    </div>
                                </div>
                                <div id="previewContent" class="bg-white rounded-lg p-3 border border-blue-200 max-h-64 overflow-y-auto">
                                    <!-- Preview content will be inserted here -->
                                </div>
                                <div class="mt-3 flex items-center justify-between text-sm">
                                    <span id="recordCount" class="text-blue-700 font-medium"></span>
                                    <button type="button" onclick="clearFilePreview()" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-times mr-1"></i>Remove File
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-end pt-8 border-t border-gray-200 space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="<?php echo e(route('company.appointments.index')); ?>" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-lg">
                    <i class="fas fa-calendar-plus mr-2"></i>Create Appointment
                </button>
            </div>
                </form>
            </div>

        <!-- Sidebar -->
        <div class="space-y-8 sticky top-8 self-start">
            
            <!-- Instructions Card -->
            <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb text-yellow-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Quick Guide</h3>
                        <p class="text-gray-600 text-sm">Follow these steps to create an appointment</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 font-bold text-sm">1</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Select Date & Time</h4>
                            <p class="text-sm text-gray-600">Choose a date at least 4 days in advance</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-purple-600 font-bold text-sm">2</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Choose Medical Test</h4>
                            <p class="text-sm text-gray-600">Select examination package or individual test</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-emerald-600 font-bold text-sm">3</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Add Patient Data</h4>
                            <p class="text-sm text-gray-600">Upload Excel file or add manually later</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Excel Template Card -->
            <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-excel text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Excel Template</h3>
                        <p class="text-gray-600 text-sm">Required format for patient data upload</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-gray-300">
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">First Name</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Last Name</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Age</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Sex</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Email</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Phone</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-gray-600">
                                    <td class="py-2 px-2">John</td>
                                    <td class="py-2 px-2">Doe</td>
                                    <td class="py-2 px-2">30</td>
                                    <td class="py-2 px-2">Male</td>
                                    <td class="py-2 px-2">john@email.com</td>
                                    <td class="py-2 px-2">123-456-7890</td>
                                    <td class="py-2 px-2">123 Main St, City</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Make sure your Excel file follows this exact format for successful import.
                    </p>
                </div>
            </div>

            <!-- Selected Test Info Card -->
            <div id="selectedTestInfo" class="content-card rounded-xl p-8 shadow-lg border border-gray-200" style="display: none;">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Selected Test</h3>
                        <p class="text-gray-600 text-sm">Your chosen medical examination</p>
                    </div>
                </div>
                <div id="testDetails">
                    <!-- Test details will be populated by JavaScript -->
                </div>
            </div>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Booked dates and minimum date from backend
    const bookedDates = <?php echo json_encode($bookedDates, 15, 512) ?>;
    const minDate = '<?php echo e($minDate); ?>';
    const appointmentDateInput = document.getElementById('appointment_date');
    const dateAvailabilityMessage = document.getElementById('date-availability-message');
    
    // Function to check date availability
    function checkDateAvailability(selectedDate) {
        if (!selectedDate) {
            dateAvailabilityMessage.style.display = 'none';
            return;
        }
        
        const today = new Date().toISOString().split('T')[0];
        
        // Check if selected date is today or in the past
        if (selectedDate <= today) {
            dateAvailabilityMessage.innerHTML = '<i class="fas fa-ban text-gray-500 mr-1"></i><span class="text-gray-600">Cannot schedule appointments for today or past dates.</span>';
            dateAvailabilityMessage.style.display = 'block';
            return false;
        }
        
        // Check if selected date is a Sunday (Saturdays are now allowed)
        const selectedDateObj = new Date(selectedDate);
        const dayOfWeek = selectedDateObj.getDay(); // 0 = Sunday, 6 = Saturday
        if (dayOfWeek === 0) {
            dateAvailabilityMessage.innerHTML = '<i class="fas fa-calendar-times text-gray-500 mr-1"></i><span class="text-gray-600">Appointments cannot be scheduled on Sundays. Please select Monday-Saturday.</span>';
            dateAvailabilityMessage.style.display = 'block';
            return false;
        }
        
        if (selectedDate < minDate) {
            dateAvailabilityMessage.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i><span class="text-yellow-600">Appointments must be scheduled at least 4 days in advance.</span>';
            dateAvailabilityMessage.style.display = 'block';
            return false;
        }
        
        if (bookedDates.includes(selectedDate)) {
            dateAvailabilityMessage.innerHTML = '<i class="fas fa-times-circle text-red-500 mr-1"></i><span class="text-red-600">Date not available - Another company has already booked this date.</span>';
            dateAvailabilityMessage.style.display = 'block';
            return false;
        }
        
        dateAvailabilityMessage.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i><span class="text-green-600">Date is available for booking.</span>';
        dateAvailabilityMessage.style.display = 'block';
        return true;
    }
    
    // Check date availability when date changes
    appointmentDateInput.addEventListener('change', function() {
        checkDateAvailability(this.value);
    });
    
    // Check initial date if pre-filled
    if (appointmentDateInput.value) {
        checkDateAvailability(appointmentDateInput.value);
    }
    
    // Disable dates that are too early (today and next 3 days) and Sundays
    function disableInvalidDates() {
        // Add CSS to grey out disabled dates and weekends
        const style = document.createElement('style');
        style.textContent = `
            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(0.8);
            }
            input[type="date"] {
                position: relative;
            }
            /* Hide weekend dates in some browsers */
            input[type="date"]::-webkit-datetime-edit-day-field[disabled],
            input[type="date"]::-webkit-datetime-edit-month-field[disabled],
            input[type="date"]::-webkit-datetime-edit-year-field[disabled] {
                color: #9ca3af;
                background-color: #f3f4f6;
            }
        `;
        document.head.appendChild(style);
        
        // Add event listener to prevent weekend selection
        appointmentDateInput.addEventListener('input', function(e) {
            const selectedDate = e.target.value;
            if (selectedDate) {
                const selectedDateObj = new Date(selectedDate);
                const dayOfWeek = selectedDateObj.getDay();
                
                // If Sunday is selected, clear the input and show message
                if (dayOfWeek === 0) {
                    e.target.value = '';
                    dateAvailabilityMessage.innerHTML = '<i class="fas fa-calendar-times text-gray-500 mr-1"></i><span class="text-gray-600">Sundays are not available for appointments. Please select Monday-Saturday.</span>';
                    dateAvailabilityMessage.style.display = 'block';
                }
            }
        });
    }
    
    disableInvalidDates();
    const selectedTestInfo = document.getElementById('selectedTestInfo');
    const testDetails = document.getElementById('testDetails');
    
    // Medical test category selection handling - Allow one test per category
    const hiddenCategoryInput = document.getElementById('medical_test_categories_id');
    const hiddenTestInput = document.getElementById('medical_test_id');
    const testCheckboxes = document.querySelectorAll('input[name="appointment_selected_test"][data-category-id]');
    const totalPriceInput = document.getElementById('total_price');

    function updateHiddenInputs() {
        const checkedTests = Array.from(testCheckboxes).filter(cb => cb.checked);
        if (checkedTests.length > 0) {
            // Build arrays of category IDs and test IDs (one per category)
            const categoryIds = [];
            const testIds = [];
            let total = 0;

            checkedTests.forEach(cb => {
                const categoryId = cb.getAttribute('data-category-id');
                const testId = cb.getAttribute('data-test-id');
                categoryIds.push(Number(categoryId));
                testIds.push(Number(testId));
                total += extractPrice(cb);
            });

            // Store as JSON arrays for backend decoding
            hiddenCategoryInput.value = JSON.stringify(categoryIds);
            hiddenTestInput.value = JSON.stringify(testIds);
            totalPriceInput.value = total.toFixed(2);
        } else {
            hiddenCategoryInput.value = '';
            hiddenTestInput.value = '';
            totalPriceInput.value = '0';
        }
    }

    function extractPrice(checkbox) {
        const testCard = checkbox.closest('label');
        const priceElement = testCard.querySelector('.text-emerald-700');
        
        if (priceElement) {
            const priceText = priceElement.textContent;
            const priceMatch = priceText.match(/[\d,]+\.?\d*/);
            if (priceMatch) {
                const price = parseFloat(priceMatch[0].replace(',', ''));
                return price;
            }
        }
        
        return 0;
    }

    function handleTestChange(e) {
        const current = e.target;
        const currentCategoryId = current.getAttribute('data-category-id');

        if (current.checked) {
            // Get category name to check if it's Blood Chemistry
            const categories = {};
            document.querySelectorAll('[id^="category-"]').forEach(categoryDiv => {
                const categoryId = categoryDiv.id.replace('category-', '');
                const categoryHeader = categoryDiv.previousElementSibling.querySelector('h4');
                if (categoryHeader) {
                    const categoryName = categoryHeader.textContent.trim().toLowerCase();
                    categories[categoryId] = categoryName;
                }
            });

            const currentCategoryName = categories[currentCategoryId];

            // Allow multiple selections for Blood Chemistry, single selection for others
            if (currentCategoryName !== 'blood chemistry') {
                // Enforce one selection PER CATEGORY: uncheck other tests in the same category only
                const sameCategory = document.querySelectorAll(`input[name="appointment_selected_test"][data-category-id="${currentCategoryId}"]`);
                Array.from(sameCategory).forEach(cb => {
                    if (cb !== current) cb.checked = false;
                });
            }
            // For Blood Chemistry, allow multiple selections (no unchecking)
        }

        // Handle package/blood chemistry blocking for both checking and unchecking
        handlePackageBloodChemistryBlocking(current);

        // Update hidden inputs with current selections
        updateHiddenInputs();
        
        // Update all category counters since we changed selections
        const allCategoryIds = [...new Set(Array.from(testCheckboxes).map(cb => cb.getAttribute('data-category-id')))]
        allCategoryIds.forEach(categoryId => {
            updateCategoryCount(categoryId);
        });
        
        // Update selected test info in sidebar
        updateSelectedTestsInfo();
    }

    function handlePackageBloodChemistryBlocking(currentCheckbox) {
        const currentCategoryId = currentCheckbox.getAttribute('data-category-id');
        
        // Get category names by finding the category elements
        const categories = {};
        document.querySelectorAll('[id^="category-"]').forEach(categoryDiv => {
            const categoryId = categoryDiv.id.replace('category-', '');
            const categoryHeader = categoryDiv.previousElementSibling.querySelector('h4');
            if (categoryHeader) {
                const categoryName = categoryHeader.textContent.trim().toLowerCase();
                categories[categoryId] = categoryName;
            }
        });

        const currentCategoryName = categories[currentCategoryId];

        if (currentCheckbox.checked) {
            if (currentCategoryName === 'package') {
                // If package is selected, disable and uncheck all blood chemistry tests
                Object.keys(categories).forEach(catId => {
                    if (categories[catId] === 'blood chemistry') {
                        const bloodChemistryCheckboxes = document.querySelectorAll(`input[data-category-id="${catId}"]`);
                        bloodChemistryCheckboxes.forEach(cb => {
                            cb.checked = false;
                            cb.disabled = true;
                            cb.closest('label').style.opacity = '0.5';
                            cb.closest('label').style.pointerEvents = 'none';
                        });
                        
                        // Show blocking notice
                        const blockingNotice = document.getElementById(`blocking-notice-${catId}`);
                        const blockingText = document.getElementById(`blocking-text-${catId}`);
                        if (blockingNotice && blockingText) {
                            blockingText.textContent = 'Blood Chemistry tests are disabled because a Package is selected. Unselect the package to enable these tests.';
                            blockingNotice.classList.remove('hidden');
                        }
                    }
                });
            } else if (currentCategoryName === 'blood chemistry') {
                // If blood chemistry is selected, disable and uncheck all package tests
                Object.keys(categories).forEach(catId => {
                    if (categories[catId] === 'package') {
                        const packageCheckboxes = document.querySelectorAll(`input[data-category-id="${catId}"]`);
                        packageCheckboxes.forEach(cb => {
                            cb.checked = false;
                            cb.disabled = true;
                            cb.closest('label').style.opacity = '0.5';
                            cb.closest('label').style.pointerEvents = 'none';
                        });
                        
                        // Show blocking notice
                        const blockingNotice = document.getElementById(`blocking-notice-${catId}`);
                        const blockingText = document.getElementById(`blocking-text-${catId}`);
                        if (blockingNotice && blockingText) {
                            blockingText.textContent = 'Package tests are disabled because Blood Chemistry is selected. Unselect blood chemistry tests to enable packages.';
                            blockingNotice.classList.remove('hidden');
                        }
                    }
                });
            }
        } else {
            // If unchecking, check if we need to re-enable the blocked category
            const stillHasSelection = Array.from(document.querySelectorAll(`input[data-category-id="${currentCategoryId}"]`))
                .some(cb => cb.checked);

            if (!stillHasSelection) {
                if (currentCategoryName === 'package') {
                    // Re-enable blood chemistry tests
                    Object.keys(categories).forEach(catId => {
                        if (categories[catId] === 'blood chemistry') {
                            const bloodChemistryCheckboxes = document.querySelectorAll(`input[data-category-id="${catId}"]`);
                            bloodChemistryCheckboxes.forEach(cb => {
                                cb.disabled = false;
                                cb.closest('label').style.opacity = '1';
                                cb.closest('label').style.pointerEvents = 'auto';
                            });
                            
                            // Hide blocking notice
                            const blockingNotice = document.getElementById(`blocking-notice-${catId}`);
                            if (blockingNotice) {
                                blockingNotice.classList.add('hidden');
                            }
                        }
                    });
                } else if (currentCategoryName === 'blood chemistry') {
                    // Re-enable package tests
                    Object.keys(categories).forEach(catId => {
                        if (categories[catId] === 'package') {
                            const packageCheckboxes = document.querySelectorAll(`input[data-category-id="${catId}"]`);
                            packageCheckboxes.forEach(cb => {
                                cb.disabled = false;
                                cb.closest('label').style.opacity = '1';
                                cb.closest('label').style.pointerEvents = 'auto';
                            });
                            
                            // Hide blocking notice
                            const blockingNotice = document.getElementById(`blocking-notice-${catId}`);
                            if (blockingNotice) {
                                blockingNotice.classList.add('hidden');
                            }
                        }
                    });
                }
            }
        }
    }

    function updateSelectedTestsInfo() {
        const checkedBoxes = Array.from(testCheckboxes).filter(cb => cb.checked);
        
        if (checkedBoxes.length === 0) {
            if (selectedTestInfo) selectedTestInfo.style.display = 'none';
            return;
        }

        let totalPrice = 0;
        let testsHtml = '<div class="space-y-4">';
        
        checkedBoxes.forEach(checkbox => {
            const testCard = checkbox.closest('label');
            const testName = testCard.querySelector('.text-base.font-bold').textContent;
            const description = testCard.querySelector('.text-sm.text-gray-600')?.textContent || '';
            const price = extractPrice(checkbox);
            
            totalPrice += price;
            
            testsHtml += `
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900">${testName}</h4>
                    ${description ? `<p class="text-xs text-gray-600 mt-1">${description}</p>` : ''}
                    ${price > 0 ? `<p class="text-sm font-bold text-emerald-600 mt-2">₱${price.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>` : '<p class="text-sm text-blue-600 mt-2">Contact for pricing</p>'}
                </div>
            `;
        });
        
        testsHtml += '</div>';
        
        if (totalPrice > 0) {
            testsHtml += `
                <div class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-emerald-800">Total Price:</span>
                        <span class="text-xl font-bold text-emerald-600">₱${totalPrice.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                    </div>
                </div>
            `;
        }
        
        if (testDetails) {
            testDetails.innerHTML = testsHtml;
        }
        if (selectedTestInfo) {
            selectedTestInfo.style.display = 'block';
        }
    }

    testCheckboxes.forEach(cb => cb.addEventListener('change', handleTestChange));
    
    // Initialize on page load
    updateHiddenInputs();
    
    // Initialize blocking state on page load (for form validation errors)
    testCheckboxes.forEach(cb => {
        if (cb.checked) {
            handlePackageBloodChemistryBlocking(cb);
        }
    });
    
    // Prevent form submission if date is not available
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const selectedDate = appointmentDateInput.value;
        if (!checkDateAvailability(selectedDate)) {
            e.preventDefault();
            alert('Please select an available date before submitting the appointment.');
            return;
        }
        
        // Check for selected tests before submission
        const checkedTests = Array.from(testCheckboxes).filter(cb => cb.checked);
        if (checkedTests.length === 0) {
            e.preventDefault();
            alert('Please select at least one medical test before submitting.');
            return;
        }
    });
});

// Collapsible category functions
function toggleCategory(categoryId) {
    const content = document.getElementById(categoryId);
    const chevron = document.getElementById('chevron-' + categoryId.replace('category-', ''));
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        chevron.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }
}

function updateCategoryCount(categoryId) {
    const categoryCheckboxes = document.querySelectorAll(`input[data-category-id="${categoryId}"]`);
    const checkedCount = Array.from(categoryCheckboxes).filter(cb => cb.checked).length;
    const countElement = document.getElementById(`selected-count-${categoryId}`);
    
    if (countElement) {
        if (checkedCount > 0) {
            countElement.textContent = `${checkedCount} selected`;
            countElement.classList.remove('hidden');
        } else {
            countElement.classList.add('hidden');
        }
    }
}

// Add form validation before submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="appointments"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const hiddenCategoryInput = document.getElementById('medical_test_categories_id');
            const hiddenTestInput = document.getElementById('medical_test_id');
            
            // Validate JSON arrays of selections (one test per category)
            if (!hiddenCategoryInput.value || !hiddenTestInput.value) {
                e.preventDefault();
                alert('Please select at least one medical test before submitting the appointment.');
                return false;
            }

            let categoryIds, testIds;
            try {
                categoryIds = JSON.parse(hiddenCategoryInput.value);
                testIds = JSON.parse(hiddenTestInput.value);
            } catch (err) {
                e.preventDefault();
                alert('Invalid test selection data. Please reselect your tests.');
                return false;
            }

            if (!Array.isArray(categoryIds) || !Array.isArray(testIds) || categoryIds.length === 0 || categoryIds.length !== testIds.length) {
                e.preventDefault();
                alert('Please ensure one test is selected per category.');
                return false;
            }

            // Ensure numeric values
            const numericOk = categoryIds.every(id => !isNaN(id)) && testIds.every(id => !isNaN(id));
            if (!numericOk) {
                e.preventDefault();
                alert('Invalid test selection. Please refresh the page and try again.');
                return false;
            }
        });
    }

    // File preview functionality
    const fileInput = document.getElementById('excel_file');
    const filePreview = document.getElementById('filePreview');
    
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            previewExcelFile(file);
        } else {
            clearFilePreview();
        }
    });

    function previewExcelFile(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1});
                
                displayPreview(file.name, jsonData);
            } catch (error) {
                console.error('Error reading Excel file:', error);
                alert('Error reading Excel file. Please make sure it\'s a valid Excel file.');
            }
        };
        reader.readAsArrayBuffer(file);
    }

    function displayPreview(fileName, data) {
        document.getElementById('fileName').textContent = fileName;
        
        if (data.length === 0) {
            document.getElementById('previewContent').innerHTML = '<p class="text-gray-500 text-sm">No data found in the file.</p>';
            document.getElementById('recordCount').textContent = '0 records';
            filePreview.classList.remove('hidden');
            return;
        }

        // Create preview table
        let html = '<div class="overflow-x-auto"><table class="w-full text-xs border-collapse">';
        
        // Header row
        if (data[0]) {
            html += '<thead><tr class="bg-gray-50 border-b">';
            const expectedHeaders = ['First Name', 'Last Name', 'Age', 'Sex', 'Email', 'Phone', 'Address'];
            for (let i = 0; i < Math.max(data[0].length, expectedHeaders.length); i++) {
                const header = data[0][i] || expectedHeaders[i] || `Column ${i + 1}`;
                html += `<th class="text-left py-2 px-2 font-medium text-gray-700 border-r">${header}</th>`;
            }
            html += '</tr></thead>';
        }

        // Data rows (show first 10 rows)
        html += '<tbody>';
        const maxRows = Math.min(data.length, 11); // Show up to 10 data rows (excluding header)
        for (let i = 1; i < maxRows; i++) {
            html += '<tr class="border-b hover:bg-gray-50">';
            const maxCols = Math.max(data[i] ? data[i].length : 0, 7); // Ensure 7 columns for address
            for (let j = 0; j < maxCols; j++) {
                const cellValue = data[i] && data[i][j] !== undefined ? data[i][j] : '';
                html += `<td class="py-2 px-2 border-r text-gray-600">${cellValue}</td>`;
            }
            html += '</tr>';
        }
        
        if (data.length > 11) {
            html += `<tr><td colspan="7" class="py-2 px-2 text-center text-gray-500 italic">... and ${data.length - 11} more rows</td></tr>`;
        }
        
        html += '</tbody></table></div>';
        
        document.getElementById('previewContent').innerHTML = html;
        document.getElementById('recordCount').textContent = `${data.length - 1} records found`;
        filePreview.classList.remove('hidden');
    }

    window.clearFilePreview = function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
        document.getElementById('previewContent').innerHTML = '';
        document.getElementById('fileName').textContent = '';
        document.getElementById('recordCount').textContent = '';
    };
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.company', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rss_new\resources\views/company/appointments/create.blade.php ENDPATH**/ ?>