<?php $__env->startSection('title', 'Create Pre-Employment Medical Examination - RSS Citi Health Services'); ?>
<?php $__env->startSection('page-title', 'Create Pre-Employment Examination'); ?>
<?php $__env->startSection('page-description', 'New employment medical screening and health assessment'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Validation Errors -->
    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-red-800 font-semibold mb-2">Please complete all required fields:</h3>
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

    <!-- Header Section -->
    <div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <i class="fas fa-plus-circle text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Create Pre-Employment Medical Examination</h2>
                        <p class="text-blue-100 text-sm">Certificate of medical examination for employment screening</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white/90 text-sm">Form Type</div>
                    <div class="text-white font-bold text-lg">Pre-Employment</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employment Record Information Card -->
    <?php if($preEmploymentRecord): ?>
    <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-briefcase text-blue-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Employment Record Information</h3>
                <p class="text-gray-600 text-sm">Candidate details for pre-employment medical screening</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Candidate Name</label>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold text-sm">
                            <?php echo e(substr($preEmploymentRecord->first_name ?? 'C', 0, 1)); ?><?php echo e(substr($preEmploymentRecord->last_name ?? 'A', 0, 1)); ?>

                        </span>
                    </div>
                    <div class="text-lg font-semibold text-gray-900"><?php echo e($preEmploymentRecord->full_name ?? ($preEmploymentRecord->first_name . ' ' . $preEmploymentRecord->last_name)); ?></div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Age</label>
                <div class="text-lg font-semibold text-gray-900"><?php echo e($preEmploymentRecord->age); ?> years old</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Gender</label>
                <div class="text-lg font-semibold text-gray-900"><?php echo e(ucfirst($preEmploymentRecord->sex)); ?></div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Company</label>
                <div class="text-sm font-medium text-gray-900"><?php echo e($preEmploymentRecord->company_name); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Examination Form -->
    <form action="<?php echo e(route('nurse.pre-employment.store')); ?>" method="POST" class="space-y-8">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="pre_employment_record_id" value="<?php echo e($preEmploymentRecord->id); ?>">
        
        <?php
            $medicalTestName = $preEmploymentRecord->medicalTest->name ?? '';
            $lowerTestName = strtolower($medicalTestName);
            $isAudiometryIshiharaOnly = $lowerTestName === 'audiometry and ishihara only';
            
            // Check for drug test in medical test name
            $hasDrugTest = in_array($lowerTestName, [
                'pre-employment with drug test',
                'pre-employment with ecg and drug test',
                'pre-employment with drug test and audio and ishihara',
                'drug test only (bring valid i.d)'
            ]) || str_contains($lowerTestName, 'drug test');
        ?>

        <?php if(!$isAudiometryIshiharaOnly): ?>
        <!-- Physical Examination Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Physical Examination</h3>
                    <p class="text-gray-600 text-sm">Vital signs and basic physical measurements</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php $phys = old('physical_exam', []); ?>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Temperature <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[temp]" value="<?php echo e($phys['temp'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['physical_exam.temp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="e.g., 36.5°C" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-thermometer-half text-gray-400"></i>
                        </div>
                    </div>
                    <?php $__errorArgs = ['physical_exam.temp'];
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

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Height <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[height]" value="<?php echo e($phys['height'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['physical_exam.height'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="e.g., 170 cm" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-ruler-vertical text-gray-400"></i>
                        </div>
                    </div>
                    <?php $__errorArgs = ['physical_exam.height'];
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

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Weight <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[weight]" value="<?php echo e($phys['weight'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['physical_exam.weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="e.g., 65 kg" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-weight text-gray-400"></i>
                        </div>
                    </div>
                    <?php $__errorArgs = ['physical_exam.weight'];
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

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Heart Rate <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="physical_exam[heart_rate]" value="<?php echo e($phys['heart_rate'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['physical_exam.heart_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="e.g., 72 bpm" required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-heartbeat text-gray-400"></i>
                        </div>
                    </div>
                    <?php $__errorArgs = ['physical_exam.heart_rate'];
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
        </div>
        <?php else: ?>
        <!-- Hidden inputs for physical examination when Audiometry and Ishihara Only -->
        <input type="hidden" name="physical_exam[temp]" value="N/A - Not required for Audiometry and Ishihara Only" />
        <input type="hidden" name="physical_exam[height]" value="N/A - Not required for Audiometry and Ishihara Only" />
        <input type="hidden" name="physical_exam[weight]" value="N/A - Not required for Audiometry and Ishihara Only" />
        <input type="hidden" name="physical_exam[heart_rate]" value="N/A - Not required for Audiometry and Ishihara Only" />
        <?php endif; ?>
        
        <?php if(!$isAudiometryIshiharaOnly): ?>
        <!-- Skin Identification Marks Card -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-amber-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Skin Marks & Tattoos</h3>
                    <p class="text-gray-600 text-sm">Notable skin marks, scars, tattoos, or identifying features</p>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                    Skin Marks Description <span class="text-red-500">*</span>
                </label>
                <textarea name="skin_marks" rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['skin_marks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          placeholder="Describe any visible skin marks, scars, tattoos, or identifying features..." required><?php echo e(old('skin_marks')); ?></textarea>
                <?php $__errorArgs = ['skin_marks'];
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
        <?php else: ?>
        <!-- Hidden input for skin marks when Audiometry and Ishihara Only -->
        <input type="hidden" name="skin_marks" value="N/A - Not required for Audiometry and Ishihara Only" />
        <?php endif; ?>
        
        <!-- Vision Tests Card - Show for Audiometry and Ishihara Only (for Ishihara test) or other tests (for visual acuity) -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        <?php if($isAudiometryIshiharaOnly): ?>
                            Ishihara Color Vision Test
                        <?php else: ?>
                            Vision Tests
                        <?php endif; ?>
                    </h3>
                    <p class="text-gray-600 text-sm">
                        <?php if($isAudiometryIshiharaOnly): ?>
                            Color vision assessment using Ishihara test
                        <?php else: ?>
                            Visual acuity and color vision assessments
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 <?php if(!$isAudiometryIshiharaOnly): ?> md:grid-cols-2 <?php endif; ?> gap-6">
                <?php if(!$isAudiometryIshiharaOnly): ?>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Visual Acuity <span class="text-red-500">*</span>
                    </label>
                    <textarea name="visual" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['visual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              placeholder="Record visual acuity test results (e.g., 20/20, corrected/uncorrected vision)" required><?php echo e(old('visual')); ?></textarea>
                    <?php $__errorArgs = ['visual'];
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
                <?php else: ?>
                <!-- Hidden input for visual acuity when Audiometry and Ishihara Only -->
                <input type="hidden" name="visual" value="N/A - Not required for Audiometry and Ishihara Only" />
                <?php endif; ?>

                <?php if($isAudiometryIshiharaOnly || in_array(strtolower($medicalTestName), ['pre-employment with drug test and audio and ishihara'])): ?>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Ishihara Test <span class="text-red-500">*</span>
                    </label>
                    <textarea name="ishihara_test" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['ishihara_test'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-2 ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              placeholder="Record color vision test results (e.g., Normal, Color blind - specify type)" required><?php echo e(old('ishihara_test')); ?></textarea>
                    <?php $__errorArgs = ['ishihara_test'];
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
                <?php else: ?>
                <!-- Ishihara test hidden for other pre-employment medical tests -->
                <input type="hidden" name="ishihara_test" value="N/A - Not required for this medical test" />
                <?php endif; ?>
            </div>
        </div>
        
        <?php if($hasDrugTest): ?>
        <!-- Drug Test Form Component -->
        <?php if (isset($component)) { $__componentOriginala2bdae37886025e013e70a6920df9a26 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala2bdae37886025e013e70a6920df9a26 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.drug-test-form','data' => ['examType' => 'pre-employment','patientData' => [
                'name' => $preEmploymentRecord->full_name ?? ($preEmploymentRecord->first_name . ' ' . $preEmploymentRecord->last_name),
                'address' => $preEmploymentRecord->address ?? '',
                'age' => $preEmploymentRecord->age ?? '',
                'gender' => ucfirst($preEmploymentRecord->sex ?? '')
            ],'isEdit' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('drug-test-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['exam-type' => 'pre-employment','patient-data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                'name' => $preEmploymentRecord->full_name ?? ($preEmploymentRecord->first_name . ' ' . $preEmploymentRecord->last_name),
                'address' => $preEmploymentRecord->address ?? '',
                'age' => $preEmploymentRecord->age ?? '',
                'gender' => ucfirst($preEmploymentRecord->sex ?? '')
            ]),'is-edit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala2bdae37886025e013e70a6920df9a26)): ?>
<?php $attributes = $__attributesOriginala2bdae37886025e013e70a6920df9a26; ?>
<?php unset($__attributesOriginala2bdae37886025e013e70a6920df9a26); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala2bdae37886025e013e70a6920df9a26)): ?>
<?php $component = $__componentOriginala2bdae37886025e013e70a6920df9a26; ?>
<?php unset($__componentOriginala2bdae37886025e013e70a6920df9a26); ?>
<?php endif; ?>
        <?php endif; ?>

        <!-- Signature Section -->
        <div class="content-card rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-signature text-gray-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Medical Technologist Signature</h3>
                    <p class="text-gray-600 text-sm">Examination completed by: <?php echo e(Auth::user()->fname); ?> <?php echo e(Auth::user()->lname); ?></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Medical Technologist</p>
                        <div class="border-b-2 border-gray-400 w-64 mt-3 mb-2"></div>
                        <p class="text-xs text-gray-500"><?php echo e(Auth::user()->fname); ?> <?php echo e(Auth::user()->lname); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Date</p>
                        <div class="border-b-2 border-gray-400 w-32 mt-3 mb-2"></div>
                        <p class="text-xs text-gray-500"><?php echo e(now()->format('M d, Y')); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-end pt-8 border-t border-gray-200 space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="<?php echo e(route('nurse.pre-employment')); ?>" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" 
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-lg">
                <i class="fas fa-save mr-2"></i>Create Examination
            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth animations to content cards
        const contentCards = document.querySelectorAll('.content-card');
        contentCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in-up');
        });

        // Form validation enhancement
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                } else {
                    this.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                    this.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-200');
                }
            });

            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'ring-red-200');
                    this.classList.add('border-emerald-500', 'ring-emerald-200');
                }
            });
        });

        // Form submission confirmation
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('input[required], textarea[required]');
            let hasErrors = false;
            
            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    hasErrors = true;
                    field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert('Please fill in all required fields before submitting.');
                return;
            }
            
            if (!confirm('Are you sure you want to create this pre-employment medical examination?')) {
                e.preventDefault();
            }
        });
    });
</script>

<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out forwards;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nurse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rss_new\resources\views/nurse/pre-employment-create.blade.php ENDPATH**/ ?>