@extends('layouts.radiologist')

@section('title', 'Pre-Employment X-Ray Review')
@section('page-title', 'Pre-Employment X-Ray Review')
@section('page-description', 'Chest X-Ray Analysis & Interpretation')

@section('content')
<div class="space-y-6">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center space-x-3 shadow-sm">
            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-emerald-800 font-semibold">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-purple-700 to-purple-800 rounded-xl shadow-lg overflow-hidden">
        <div class="px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">
                        <i class="fas fa-x-ray mr-2"></i>Chest X-Ray Review
                    </h1>
                    <p class="text-purple-200">Pre-Employment Examination</p>
                </div>
                <div class="hidden md:block">
                    <a href="{{ route('radiologist.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors border border-white/20 backdrop-blur-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - X-Ray Image -->
        <div class="lg:col-span-2 space-y-6">
            @if(isset($checklist) && $checklist && $checklist->xray_image_path)
                <!-- X-Ray Image Viewer -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-image text-purple-600 mr-2"></i>X-Ray Image
                            </h3>
                            <span class="text-xs bg-purple-600 text-white px-3 py-1 rounded-full font-medium">
                                <i class="fas fa-expand-arrows-alt mr-1"></i>Click to Enlarge
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="relative bg-slate-900 rounded-lg overflow-hidden border-2 border-slate-700 cursor-pointer hover:border-purple-500 transition-colors group" id="xray-thumb">
                            <img src="{{ asset('storage/' . $checklist->xray_image_path) }}" 
                                 alt="Chest X-Ray" 
                                 class="w-full h-auto object-contain" 
                                 style="min-height: 400px; max-height: 600px;" />
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="bg-white/90 backdrop-blur-sm rounded-full p-4">
                                        <i class="fas fa-search-plus text-purple-700 text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-center space-x-2 text-sm text-slate-600">
                            <i class="fas fa-info-circle"></i>
                            <span>Click image for fullscreen viewer with zoom and pan controls</span>
                        </div>
                    </div>
                </div>

                <!-- Fullscreen Viewer -->
                <div id="image-viewer-overlay" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50">
                    <div class="absolute top-4 right-4 flex items-center space-x-2">
                        <button type="button" id="zoom-out" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg shadow-lg transition-colors">
                            <i class="fas fa-search-minus mr-2"></i>Zoom Out
                        </button>
                        <button type="button" id="zoom-reset" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg shadow-lg transition-colors">
                            <i class="fas fa-compress mr-2"></i>Reset
                        </button>
                        <button type="button" id="zoom-in" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg shadow-lg transition-colors">
                            <i class="fas fa-search-plus mr-2"></i>Zoom In
                        </button>
                        <button type="button" id="close-viewer" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Close
                        </button>
                    </div>
                    <div class="absolute top-4 left-4 bg-slate-800/90 backdrop-blur-sm text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-mouse mr-2"></i>Scroll to zoom • Drag to pan • ESC to close
                    </div>
                    <div id="viewer-canvas" class="w-full h-full flex items-center justify-center overflow-hidden cursor-grab">
                        <img id="viewer-image" src="{{ asset('storage/' . $checklist->xray_image_path) }}" alt="X-Ray Full" class="select-none" draggable="false" />
                    </div>
                </div>
            @else
                <!-- No Image Available -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-image text-purple-600 mr-2"></i>X-Ray Image
                        </h3>
                    </div>
                    <div class="p-12">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-image text-purple-400 text-3xl"></i>
                            </div>
                            <p class="text-gray-600 font-medium">No X-Ray Image Available</p>
                            <p class="text-sm text-gray-500 mt-2">The X-ray image has not been uploaded yet</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Applicant Info & Review Form -->
        <div class="space-y-6">
            <!-- Applicant Information Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user-tie text-purple-600 mr-2"></i>Applicant Information
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-circle text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Full Name</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $full_name }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-venus-mars text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sex</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $sex ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-birthday-cake text-indigo-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Age</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $age ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-amber-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Company</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $company ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Form Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-clipboard-check text-purple-600 mr-2"></i>X-Ray Review
                    </h3>
                </div>
                <form action="{{ route('radiologist.pre-employment.update', request()->route('id')) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Test Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-x-ray text-purple-600 mr-2"></i>Examination
                        </label>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">Chest X-Ray</p>
                            <p class="text-xs text-gray-500 mt-1">Posteroanterior (PA) View</p>
                        </div>
                    </div>

                    <!-- Result -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-stethoscope text-purple-600 mr-2"></i>Result
                        </label>
                        <select name="cxr_result" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-sm font-medium">
                            <option value="—" {{ old('cxr_result', $cxr_result) == '—' ? 'selected' : '' }}>— Select Result —</option>
                            <option value="Normal" {{ old('cxr_result', $cxr_result) == 'Normal' ? 'selected' : '' }}>✓ Normal</option>
                            <option value="Not Normal" {{ old('cxr_result', $cxr_result) == 'Not Normal' ? 'selected' : '' }}>⚠ Not Normal</option>
                        </select>
                    </div>

                    <!-- Findings -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-notes-medical text-purple-600 mr-2"></i>Clinical Findings
                        </label>
                        <textarea name="cxr_finding" 
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-sm resize-none"
                                  placeholder="Enter detailed findings and observations...">{{ old('cxr_finding', $cxr_finding) }}</textarea>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Provide detailed radiological interpretation
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <a href="{{ route('radiologist.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                            <i class="fas fa-save mr-2"></i>Save Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    var thumb = document.getElementById('xray-thumb');
    if(!thumb) return;
    var overlay = document.getElementById('image-viewer-overlay');
    var img = document.getElementById('viewer-image');
    var canvas = document.getElementById('viewer-canvas');
    var btnIn = document.getElementById('zoom-in');
    var btnOut = document.getElementById('zoom-out');
    var btnReset = document.getElementById('zoom-reset');
    var btnClose = document.getElementById('close-viewer');

    var scale = 1;
    var minScale = 0.25;
    var maxScale = 6;
    var translateX = 0, translateY = 0;
    var isPanning = false; var startX = 0; var startY = 0;

    function applyTransform(){
        img.style.transform = 'translate(' + translateX + 'px,' + translateY + 'px) scale(' + scale + ')';
        img.style.transformOrigin = 'center center';
        img.style.maxWidth = 'none';
        img.style.maxHeight = 'none';
    }

    function enterFullscreen(el){
        if(el.requestFullscreen){ el.requestFullscreen(); }
        else if(el.webkitRequestFullscreen){ el.webkitRequestFullscreen(); }
        else if(el.msRequestFullscreen){ el.msRequestFullscreen(); }
    }

    function exitFullscreen(){
        if(document.exitFullscreen){ document.exitFullscreen(); }
        else if(document.webkitExitFullscreen){ document.webkitExitFullscreen(); }
        else if(document.msExitFullscreen){ document.msExitFullscreen(); }
    }

    function openViewer(){
        overlay.classList.remove('hidden');
        scale = 1; translateX = 0; translateY = 0; applyTransform();
        setTimeout(function(){ enterFullscreen(overlay); }, 0);
    }

    function closeViewer(){
        overlay.classList.add('hidden');
        exitFullscreen();
    }

    function zoom(delta, centerX, centerY){
        var oldScale = scale;
        scale = Math.min(maxScale, Math.max(minScale, scale * delta));
        var rect = img.getBoundingClientRect();
        var cx = centerX - rect.left; var cy = centerY - rect.top;
        var factor = scale / oldScale;
        translateX = (translateX - cx) * factor + cx;
        translateY = (translateY - cy) * factor + cy;
        applyTransform();
    }

    thumb.addEventListener('click', openViewer);
    btnClose.addEventListener('click', closeViewer);
    btnIn.addEventListener('click', function(){ zoom(1.2, window.innerWidth/2, window.innerHeight/2); });
    btnOut.addEventListener('click', function(){ zoom(1/1.2, window.innerWidth/2, window.innerHeight/2); });
    btnReset.addEventListener('click', function(){ scale = 1; translateX = 0; translateY = 0; applyTransform(); });

    canvas.addEventListener('wheel', function(e){
        e.preventDefault();
        var delta = e.deltaY < 0 ? 1.1 : 1/1.1;
        zoom(delta, e.clientX, e.clientY);
    }, { passive: false });

    canvas.addEventListener('mousedown', function(e){
        isPanning = true; startX = e.clientX - translateX; startY = e.clientY - translateY; 
        canvas.classList.remove('cursor-grab'); canvas.classList.add('cursor-grabbing');
    });
    window.addEventListener('mouseup', function(){
        isPanning = false; canvas.classList.remove('cursor-grabbing'); canvas.classList.add('cursor-grab');
    });
    window.addEventListener('mousemove', function(e){
        if(!isPanning) return; translateX = e.clientX - startX; translateY = e.clientY - startY; applyTransform();
    });

    document.addEventListener('keydown', function(e){
        if(overlay.classList.contains('hidden')) return;
        if(e.key === 'Escape') closeViewer();
        if(e.key === '+' || e.key === '=') btnIn.click();
        if(e.key === '-' || e.key === '_') btnOut.click();
        if(e.key === '0') btnReset.click();
    });
})();
</script>
@endpush
@endsection


