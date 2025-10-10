@extends('layouts.radiologist')

@section('title', 'OPD X-Ray Review - ' . $name)
@section('page-title', 'OPD X-Ray Review')
@section('page-description', 'Review and analyze OPD chest X-ray image')

@section('content')
<div class="space-y-8">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-200 rounded-xl p-6 flex items-center space-x-4 shadow-lg">
            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-emerald-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-emerald-800 font-semibold text-lg">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 flex items-center space-x-4 shadow-lg">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-red-800 font-semibold text-lg">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border-2 border-white/20">
                        <i class="fas fa-walking text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">OPD X-Ray Review</h2>
                        <p class="text-purple-100 text-sm">Patient: {{ $name }} ({{ $number }})</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('radiologist.opd.xray') }}" 
                       class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Patient Information -->
        <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-user mr-3"></i>Patient Information
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Patient Name</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $name ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Patient ID</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Age</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $age ? $age . ' years old' : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Gender</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $gender ? ucfirst($gender) : 'N/A' }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Examination Date</label>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">X-Ray Technician</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $medicalChecklist->chest_xray_done_by ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- X-Ray Image -->
        <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-x-ray mr-3"></i>Chest X-Ray Image
                </h3>
            </div>
            <div class="p-6">
                @if($medicalChecklist && $medicalChecklist->xray_image_path)
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $medicalChecklist->xray_image_path) }}" 
                             alt="Chest X-Ray" 
                             class="w-full h-auto max-h-96 object-contain cursor-pointer hover:scale-105 transition-transform duration-200"
                             onclick="openImageModal(this.src)">
                        <div class="absolute top-4 right-4">
                            <button onclick="openImageModal('{{ asset('storage/' . $medicalChecklist->xray_image_path) }}')" 
                                    class="bg-black/50 hover:bg-black/70 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                <i class="fas fa-expand mr-2"></i>Enlarge
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-3 text-center">
                        <i class="fas fa-info-circle mr-1"></i>Click image to view in full size
                    </p>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-image text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No X-ray image available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- X-Ray Analysis Form -->
    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
            <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-stethoscope mr-3"></i>Radiological Analysis
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('radiologist.opd.update', $opdPatient->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- X-Ray Result -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-clipboard-check mr-2 text-emerald-600"></i>X-Ray Result
                    </label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-emerald-50 hover:border-emerald-300 transition-all duration-200 {{ old('cxr_result', $cxr_result) === 'Normal' ? 'bg-emerald-50 border-emerald-500' : '' }}">
                            <input type="radio" name="cxr_result" value="Normal" class="mr-3 text-emerald-600 focus:ring-emerald-500" {{ old('cxr_result', $cxr_result) === 'Normal' ? 'checked' : '' }}>
                            <div>
                                <div class="font-medium text-gray-900">Normal</div>
                                <div class="text-sm text-gray-600">No abnormalities detected</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 {{ old('cxr_result', $cxr_result) === 'Abnormal' ? 'bg-orange-50 border-orange-500' : '' }}">
                            <input type="radio" name="cxr_result" value="Abnormal" class="mr-3 text-orange-600 focus:ring-orange-500" {{ old('cxr_result', $cxr_result) === 'Abnormal' ? 'checked' : '' }}>
                            <div>
                                <div class="font-medium text-gray-900">Abnormal</div>
                                <div class="text-sm text-gray-600">Abnormalities present</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 {{ old('cxr_result', $cxr_result) === 'Pending' ? 'bg-blue-50 border-blue-500' : '' }}">
                            <input type="radio" name="cxr_result" value="Pending" class="mr-3 text-blue-600 focus:ring-blue-500" {{ old('cxr_result', $cxr_result) === 'Pending' ? 'checked' : '' }}>
                            <div>
                                <div class="font-medium text-gray-900">Pending</div>
                                <div class="text-sm text-gray-600">Requires further review</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Detailed Findings -->
                <div>
                    <label for="cxr_finding" class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-search mr-2 text-blue-600"></i>Detailed Findings
                    </label>
                    <textarea name="cxr_finding" id="cxr_finding" rows="6" 
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                              placeholder="Describe any findings, abnormalities, or observations...">{{ old('cxr_finding', $cxr_finding !== '—' ? $cxr_finding : '') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('radiologist.opd.xray') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors duration-200 font-semibold shadow-lg">
                        <i class="fas fa-save mr-2"></i>Submit Analysis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl max-h-full p-4">
        <button onclick="closeImageModal()" 
                class="absolute top-4 right-4 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 rounded-full transition-all duration-200 z-10">
            <i class="fas fa-times text-xl"></i>
        </button>
        <img id="modalImage" src="" alt="X-Ray Image" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection
