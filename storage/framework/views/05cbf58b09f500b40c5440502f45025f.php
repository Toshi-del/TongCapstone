<?php $__env->startSection('title', 'Pre-Employment ECG Records'); ?>

<?php $__env->startSection('page-title', 'Pre-Employment ECG Records'); ?>
<?php $__env->startSection('page-description', 'Manage ECG examinations and cardiac assessments for employment screening'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div class="mb-8 p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 flex items-center space-x-3">
        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
            <i class="fas fa-check text-white text-sm"></i>
        </div>
        <div>
            <p class="text-green-800 font-semibold"><?php echo e(session('success')); ?></p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
<?php endif; ?>

<!-- ECG Status Tabs -->
<div class="content-card rounded-xl overflow-hidden shadow-lg border border-gray-200 mb-8">
    <?php
        $currentTab = request('ecg_status', 'needs_attention');
    ?>
    
    <!-- Tab Navigation -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex space-x-1">
                <a href="<?php echo e(request()->fullUrlWithQuery(['ecg_status' => 'needs_attention'])); ?>" 
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 <?php echo e($currentTab === 'needs_attention' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50'); ?>">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Needs Attention
                    <?php
                        $needsAttentionCount = \App\Models\PreEmploymentRecord::where('status', 'approved')
                            ->whereDoesntHave('preEmploymentExamination', function($q) {
                                $q->whereNotNull('ecg')
                                  ->where('ecg', '!=', '');
                            })
                            ->count();
                    ?>
                    <span class="ml-2 px-2 py-1 text-xs rounded-full <?php echo e($currentTab === 'needs_attention' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600'); ?>">
                        <?php echo e($needsAttentionCount); ?>

                    </span>
                </a>
                
                <a href="<?php echo e(request()->fullUrlWithQuery(['ecg_status' => 'ecg_completed'])); ?>" 
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 <?php echo e($currentTab === 'ecg_completed' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50'); ?>">
                    <i class="fas fa-check-circle mr-2"></i>
                    ECG Completed
                    <?php
                        $completedCount = \App\Models\PreEmploymentRecord::where('status', 'approved')
                            ->whereHas('preEmploymentExamination', function($q) {
                                $q->whereNotNull('ecg')
                                  ->where('ecg', '!=', '');
                            })
                            ->count();
                    ?>
                    <span class="ml-2 px-2 py-1 text-xs rounded-full <?php echo e($currentTab === 'ecg_completed' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600'); ?>">
                        <?php echo e($completedCount); ?>

                    </span>
                </a>
            </div>
            
            <a href="<?php echo e(route('ecgtech.pre-employment')); ?>" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-times mr-1"></i>Clear All Filters
            </a>
        </div>
    </div>

    <!-- Additional Filters -->
    <div class="p-6">
        <form method="GET" action="<?php echo e(route('ecgtech.pre-employment')); ?>" class="space-y-6">
            <!-- Preserve current tab -->
            <input type="hidden" name="ecg_status" value="<?php echo e($currentTab); ?>">
            
            <!-- Preserve search query -->
            <?php if(request('search')): ?>
                <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
            <?php endif; ?>
            
            <!-- Filter Row: Company and Gender -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Company Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <select name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Companies</option>
                        <?php
                            $companies = $preEmployments->pluck('company_name')->filter()->unique()->sort()->values();
                        ?>
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company); ?>" <?php echo e(request('company') === $company ? 'selected' : ''); ?>>
                                <?php echo e($company); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Gender Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Genders</option>
                        <option value="male" <?php echo e(request('gender') === 'male' ? 'selected' : ''); ?>>Male</option>
                        <option value="female" <?php echo e(request('gender') === 'female' ? 'selected' : ''); ?>>Female</option>
                    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>Apply Filters
                    </button>
                    <a href="<?php echo e(request()->fullUrlWithQuery(['company' => null, 'gender' => null, 'search' => null])); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>Reset Filters
                    </a>
                </div>
                
                <!-- Active Filters Display -->
                <?php if(request()->hasAny(['company', 'gender', 'search'])): ?>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        <?php if(request('search')): ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                Search: "<?php echo e(request('search')); ?>"
                                <a href="<?php echo e(request()->fullUrlWithQuery(['search' => null])); ?>" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                            </span>
                        <?php endif; ?>
                        <?php if(request('company')): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                Company: <?php echo e(request('company')); ?>

                                <a href="<?php echo e(request()->fullUrlWithQuery(['company' => null])); ?>" class="ml-1 text-green-600 hover:text-green-800">×</a>
                            </span>
                        <?php endif; ?>
                        <?php if(request('gender')): ?>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                Gender: <?php echo e(ucfirst(request('gender'))); ?>

                                <a href="<?php echo e(request()->fullUrlWithQuery(['gender' => null])); ?>" class="ml-1 text-purple-600 hover:text-purple-800">×</a>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Enhanced Search and Filter Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-8">
    <div class="flex items-center space-x-3 mb-6">
        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-search text-white"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Search & Filter</h3>
            <p class="text-gray-600 text-sm">Find specific pre-employment records quickly</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Search Records</label>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search by patient name, company name..." 
                       class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Status</label>
                    <select id="statusFilter" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Package</label>
                    <select id="packageFilter" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">All Packages</option>
                        <option value="package a">Package A</option>
                        <option value="package b">Package B</option>
                        <option value="package c">Package C</option>
                        <option value="package d">Package D</option>
                        <option value="package e">Package E</option>
                    </select>
                </div>
            </div>
            
            <button onclick="clearFilters()" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors duration-200 flex items-center justify-center space-x-2">
                <i class="fas fa-times"></i>
                <span>Clear Filters</span>
            </button>
        </div>
    </div>
</div>

<!-- Enhanced Records Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-blue-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-briefcase text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Pre-Employment ECG Records</h2>
                    <p class="text-gray-600 text-sm mt-1">Comprehensive cardiac assessments for employment screening</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-blue-600" id="recordCount"><?php echo e($preEmployments->count()); ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">Total Records</div>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full" id="recordsTable">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Age</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Gender</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Company</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $preEmployments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preEmployment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $packageName = $preEmployment->medicalTest ? strtolower($preEmployment->medicalTest->name) : '';
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200 record-row" 
                        data-name="<?php echo e(strtolower($preEmployment->first_name . ' ' . $preEmployment->last_name)); ?>" 
                        data-company="<?php echo e(strtolower($preEmployment->company_name)); ?>" 
                        data-status="<?php echo e(strtolower($preEmployment->status)); ?>" 
                        data-package="<?php echo e($packageName); ?>">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        <?php echo e(strtoupper(substr($preEmployment->first_name, 0, 1) . substr($preEmployment->last_name, 0, 1))); ?>

                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($preEmployment->first_name); ?> <?php echo e($preEmployment->last_name); ?></p>
                                    <p class="text-xs text-gray-500">Pre-Employment Candidate</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-600 font-medium"><?php echo e($preEmployment->age); ?></td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-600 font-medium"><?php echo e($preEmployment->sex); ?></td>
                        <td class="px-6 py-6 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($preEmployment->company_name); ?></div>
                            <?php if($preEmployment->medicalTest): ?>
                                <div class="text-xs text-blue-600 font-medium mt-1"><?php echo e($preEmployment->medicalTest->name); ?></div>
                            <?php else: ?>
                                <div class="text-xs text-gray-500">No package assigned</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap">
                            <?php
                                $statusConfig = match($preEmployment->status) {
                                    'approved' => ['class' => 'bg-green-100 text-green-700 border-green-200', 'icon' => 'fas fa-check-circle'],
                                    'declined' => ['class' => 'bg-red-100 text-red-700 border-red-200', 'icon' => 'fas fa-times-circle'],
                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-700 border-yellow-200', 'icon' => 'fas fa-clock'],
                                    default => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'icon' => 'fas fa-question-circle']
                                };
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border <?php echo e($statusConfig['class']); ?>">
                                <i class="<?php echo e($statusConfig['icon']); ?> mr-1"></i>
                                <?php echo e(ucfirst($preEmployment->status)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <a href="<?php echo e(route('ecgtech.medical-checklist-page.pre-employment', $preEmployment->id)); ?>" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors duration-200 group" title="ECG Checklist">
                                    <i class="fas fa-heartbeat mr-1 group-hover:scale-110 transition-transform"></i>
                                    ECG
                                </a>
                                <a href="<?php echo e(route('ecgtech.pre-employment.edit', $preEmployment->id)); ?>" class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors duration-200 group" title="Edit ECG Data">
                                    <i class="fas fa-edit mr-1 group-hover:rotate-12 transition-transform"></i>
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr id="emptyState">
                        <td colspan="6" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-briefcase text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No pre-employment records found</p>
                                <p class="text-gray-400 text-sm">New employment screening records will appear here</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Enhanced Pagination -->
    <?php if($preEmployments->hasPages()): ?>
        <div class="px-8 py-6 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing <?php echo e($preEmployments->firstItem()); ?> to <?php echo e($preEmployments->lastItem()); ?> of <?php echo e($preEmployments->total()); ?> results
                </div>
                <div class="pagination-wrapper">
                    <?php echo e($preEmployments->links()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const packageFilter = document.getElementById('packageFilter');
    const recordCount = document.getElementById('recordCount');
    
    // Add loading states and animations
    searchInput.addEventListener('input', debounce(filterTable, 300));
    statusFilter.addEventListener('change', filterTable);
    packageFilter.addEventListener('change', filterTable);
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const status = statusFilter.value.toLowerCase();
        const packageType = packageFilter.value.toLowerCase();
        const rows = document.querySelectorAll('.record-row');
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const company = row.dataset.company.toLowerCase();
            const rowStatus = row.dataset.status.toLowerCase();
            const rowPackage = row.dataset.package.toLowerCase();
            
            const matchesSearch = name.includes(searchTerm) || company.includes(searchTerm);
            const matchesStatus = status === '' || rowStatus === status;
            const matchesPackage = packageType === '' || rowPackage.includes(packageType);
            
            if (matchesSearch && matchesStatus && matchesPackage) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update record count
        recordCount.textContent = visibleCount;
        
        // Show/hide empty state
        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? '' : 'none';
        }
        
        // Update loading state
        recordCount.style.opacity = '1';
            }
        });
        
        // Update record count with animation
        setTimeout(() => {
            recordCount.textContent = visibleCount;
            recordCount.style.opacity = '1';
        }, 150);
        
        // Show/hide empty state
        if (emptyState) {
            if (visibleCount === 0 && (searchTerm || statusFilter)) {
                emptyState.style.display = '';
                emptyState.innerHTML = `
                    <td colspan="6" class="px-8 py-12 text-center">
                        <div class="flex flex-col items-center space-y-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No records match your search</p>
                            <p class="text-gray-400 text-sm">Try adjusting your search terms or filters</p>
                            <button onclick="clearFilters()" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Clear Filters
                            </button>
                        </div>
                    </td>
                `;
            } else if (visibleCount === 0) {
                emptyState.style.display = '';
            } else {
                emptyState.style.display = 'none';
            }
        }
    }
    
    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        if (packageFilter) packageFilter.value = '';
        
        // Add clear animation
        searchInput.style.transform = 'scale(0.95)';
        statusFilter.style.transform = 'scale(0.95)';
        if (packageFilter) packageFilter.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            searchInput.style.transform = 'scale(1)';
            statusFilter.style.transform = 'scale(1)';
            if (packageFilter) packageFilter.style.transform = 'scale(1)';
        }, 150);
        
        filterTable();
    }
    
    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Add hover effects to action buttons
    const actionButtons = document.querySelectorAll('button, a');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        
        // Escape to clear filters
        if (e.key === 'Escape') {
            clearFilters();
        }
    });
    
    // Add search input focus effects
    searchInput.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
        this.parentElement.style.boxShadow = '0 4px 12px rgba(34, 197, 94, 0.15)';
    });
    
    searchInput.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
        this.parentElement.style.boxShadow = 'none';
    });
    
    // Make clearFilters globally available
    window.clearFilters = clearFilters;
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .pagination-wrapper .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        space-x: 1rem;
    }
    
    .pagination-wrapper .pagination a,
    .pagination-wrapper .pagination span {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    
    .pagination-wrapper .pagination a:hover {
        background-color: #3b82f6;
        color: white;
        transform: translateY(-1px);
    }
`;
document.head.appendChild(style);
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.ecgtech', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rss_new\resources\views/ecgtech/pre-employment.blade.php ENDPATH**/ ?>