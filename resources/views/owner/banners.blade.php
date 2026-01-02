@extends('layouts.app')

@section('title', 'Banner Management')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📢 Banner Management</h1>
            <p class="text-gray-600">Manage promotional banners for your menu page</p>
        </div>
        @if($banners->count() < 3)
        <button onclick="openAddModal()" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="fas fa-plus"></i>
            Add Banner
        </button>
        @else
        <span class="text-gray-500 text-sm">
            <i class="fas fa-info-circle"></i> Maximum 3 banners reached
        </span>
        @endif
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-lightbulb text-blue-500 mt-1"></i>
            <div>
                <p class="text-blue-800 font-medium">Banner Tips</p>
                <ul class="text-blue-700 text-sm mt-1 space-y-1">
                    <li>• Recommended size: <strong>1200 x 400 pixels</strong> (3:1 ratio)</li>
                    <li>• Max file size: 2MB | Formats: JPG, PNG, GIF, WebP</li>
                    <li>• Banners auto-rotate every 5 seconds on the menu page</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Current Banners -->
    @if($banners->count() > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-4 bg-gray-50 border-b">
            <h2 class="font-semibold text-gray-800">Current Banners ({{ $banners->count() }}/3)</h2>
        </div>
        <div class="p-4 space-y-4" id="banner-list">
            @foreach($banners as $banner)
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200" data-id="{{ $banner->id }}">
                <!-- Drag Handle -->
                <div class="cursor-move text-gray-400 hover:text-gray-600" title="Drag to reorder">
                    <i class="fas fa-grip-vertical text-xl"></i>
                </div>
                
                <!-- Preview Image -->
                <div class="w-48 h-16 rounded overflow-hidden bg-gray-200 flex-shrink-0">
                    @if($banner->image_url)
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                    @endif
                </div>
                
                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-gray-800 truncate">{{ $banner->title }}</h3>
                    @if($banner->subtitle)
                    <p class="text-sm text-gray-500 truncate">{{ $banner->subtitle }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1">
                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($banner->status_color === 'green') bg-green-100 text-green-700
                            @elseif($banner->status_color === 'blue') bg-blue-100 text-blue-700
                            @elseif($banner->status_color === 'red') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ $banner->status_label }}
                        </span>
                        
                        <!-- Link Type -->
                        @if($banner->link_type !== 'none')
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-link"></i> {{ ucfirst($banner->link_type) }}
                        </span>
                        @endif
                        
                        <!-- Expiry -->
                        @if($banner->expires_at)
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-clock"></i> Expires: {{ $banner->expires_at->format('d M Y') }}
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <!-- Toggle -->
                    <form action="{{ route('owner.banners.toggle', $banner) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg transition {{ $banner->is_active ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}" title="{{ $banner->is_active ? 'Deactivate' : 'Activate' }}">
                            <i class="fas {{ $banner->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        </button>
                    </form>
                    
                    <!-- Edit -->
                    <button onclick="openEditModal({{ json_encode($banner) }})" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    
                    <!-- Delete -->
                    <form action="{{ route('owner.banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Delete this banner?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">🖼️</div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Banners Yet</h3>
        <p class="text-gray-500 mb-6">Add promotional banners to showcase on your menu page</p>
        <button onclick="openAddModal()" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-lg font-semibold transition">
            <i class="fas fa-plus mr-2"></i> Add Your First Banner
        </button>
    </div>
    @endif

    <!-- Preview Section -->
    @if($banners->count() > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <h2 class="font-semibold text-gray-800">🔍 Live Preview</h2>
        </div>
        <div class="p-4">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-6 relative overflow-hidden">
                <div id="preview-carousel" class="relative">
                    @foreach($banners->where('is_active', true) as $index => $banner)
                    <div class="preview-slide {{ $index === 0 ? '' : 'hidden' }}">
                        @if($banner->image_url)
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover rounded-lg">
                        @else
                        <div class="w-full h-48 bg-white/20 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xl">{{ $banner->title }}</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                <!-- Dots -->
                @if($banners->where('is_active', true)->count() > 1)
                <div class="flex justify-center gap-2 mt-4">
                    @foreach($banners->where('is_active', true) as $index => $banner)
                    <button class="preview-dot w-2 h-2 rounded-full transition {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" data-index="{{ $index }}"></button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Add/Edit Modal -->
<div id="bannerModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.5);">
    <!-- Modal Backdrop (clickable to close) -->
    <div class="absolute inset-0" onclick="closeModal()"></div>
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto relative z-10">
        <div class="p-6 border-b flex items-center justify-between">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Add Banner</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="bannerForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            
            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" id="bannerTitle" required 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    placeholder="e.g., New Flavor Alert!">
            </div>
            
            <!-- Subtitle -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle (optional)</label>
                <input type="text" name="subtitle" id="bannerSubtitle"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    placeholder="e.g., Try our delicious Oreo Cheesecake Waffle">
            </div>
            
            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Banner Image *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-amber-500 transition cursor-pointer" onclick="document.getElementById('bannerImage').click()">
                    <input type="file" name="image" id="bannerImage" accept="image/*" class="hidden" onchange="previewImage(this)">
                    <div id="imagePreview" class="hidden mb-3">
                        <img id="previewImg" src="" class="max-h-32 mx-auto rounded">
                    </div>
                    <div id="uploadPlaceholder">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500">Click to upload image</p>
                        <p class="text-xs text-gray-400">Max 2MB | JPG, PNG, GIF, WebP</p>
                    </div>
                </div>
            </div>
            
            <!-- Link Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link Type</label>
                <select name="link_type" id="bannerLinkType" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 focus:border-transparent" onchange="toggleLinkUrl()">
                    <option value="none">No Link</option>
                    <option value="external">External URL</option>
                </select>
            </div>
            
            <!-- Link URL -->
            <div id="linkUrlField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                <input type="url" name="link_url" id="bannerLinkUrl"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    placeholder="https://example.com">
            </div>
            
            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date (optional)</label>
                    <input type="datetime-local" name="starts_at" id="bannerStartsAt"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date (optional)</label>
                    <input type="datetime-local" name="expires_at" id="bannerExpiresAt"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i> <span id="submitBtnText">Save Banner</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Inline Script for Modal Functions -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Modal functions - defined globally
window.openAddModal = function() {
    document.getElementById('modalTitle').textContent = 'Add Banner';
    document.getElementById('bannerForm').action = '{{ route("owner.banners.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('bannerTitle').value = '';
    document.getElementById('bannerSubtitle').value = '';
    document.getElementById('bannerLinkType').value = 'none';
    document.getElementById('bannerLinkUrl').value = '';
    document.getElementById('bannerStartsAt').value = '';
    document.getElementById('bannerExpiresAt').value = '';
    document.getElementById('bannerImage').required = true;
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    document.getElementById('submitBtnText').textContent = 'Save Banner';
    toggleLinkUrl();
    document.getElementById('bannerModal').classList.remove('hidden');
}

window.openEditModal = function(banner) {
    document.getElementById('modalTitle').textContent = 'Edit Banner';
    document.getElementById('bannerForm').action = '/owner/banners/' + banner.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('bannerTitle').value = banner.title;
    document.getElementById('bannerSubtitle').value = banner.subtitle || '';
    document.getElementById('bannerLinkType').value = banner.link_type;
    document.getElementById('bannerLinkUrl').value = banner.link_url || '';
    document.getElementById('bannerStartsAt').value = banner.starts_at ? banner.starts_at.slice(0, 16) : '';
    document.getElementById('bannerExpiresAt').value = banner.expires_at ? banner.expires_at.slice(0, 16) : '';
    document.getElementById('bannerImage').required = false;
    document.getElementById('submitBtnText').textContent = 'Update Banner';
    
    // Show existing image
    if (banner.image_url) {
        document.getElementById('previewImg').src = banner.image_url;
        document.getElementById('imagePreview').classList.remove('hidden');
        document.getElementById('uploadPlaceholder').classList.add('hidden');
    }
    
    toggleLinkUrl();
    document.getElementById('bannerModal').classList.remove('hidden');
}

window.closeModal = function() {
    document.getElementById('bannerModal').classList.add('hidden');
}

window.toggleLinkUrl = function() {
    const linkType = document.getElementById('bannerLinkType').value;
    document.getElementById('linkUrlField').classList.toggle('hidden', linkType === 'none');
}

window.previewImage = function(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('uploadPlaceholder').classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// Drag and drop reordering
const bannerList = document.getElementById('banner-list');
if (bannerList) {
    new Sortable(bannerList, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: function() {
            const order = Array.from(bannerList.children).map(el => el.dataset.id);
            fetch('{{ route("owner.banners.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            });
        }
    });
}

// Preview carousel auto-rotation
const slides = document.querySelectorAll('.preview-slide');
const dots = document.querySelectorAll('.preview-dot');
let currentSlide = 0;

if (slides.length > 1) {
    setInterval(() => {
        slides[currentSlide].classList.add('hidden');
        dots[currentSlide]?.classList.remove('bg-white');
        dots[currentSlide]?.classList.add('bg-white/50');
        
        currentSlide = (currentSlide + 1) % slides.length;
        
        slides[currentSlide].classList.remove('hidden');
        dots[currentSlide]?.classList.add('bg-white');
        dots[currentSlide]?.classList.remove('bg-white/50');
    }, 5000);
}
</script>

@endsection

