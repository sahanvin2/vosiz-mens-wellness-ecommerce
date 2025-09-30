@extends('layouts.admin-new')

@section('title', 'Video Management')
@section('subtitle', 'Upload, edit, and manage video content')

@section('content')
<div class="space-y-8">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-white mb-2">Video Management</h2>
            <p class="text-gray-400">Manage hero videos, product videos, and promotional content</p>
        </div>
        <button 
            onclick="openVideoModal()" 
            class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-purple-600 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-lg">
            <i class="fas fa-video mr-2"></i>Upload New Video
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="admin-card p-6 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Videos</p>
                    <h3 class="text-2xl font-bold text-white">{{ $totalVideos ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-video text-white text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="admin-card p-6 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Hero Videos</p>
                    <h3 class="text-2xl font-bold text-white">{{ $heroVideos ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play text-white text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="admin-card p-6 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Product Videos</p>
                    <h3 class="text-2xl font-bold text-white">{{ $productVideos ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-white text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="admin-card p-6 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Size</p>
                    <h3 class="text-2xl font-bold text-white">{{ $totalSize ?? '0 MB' }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hdd text-white text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Categories Tabs -->
    <div class="admin-card rounded-xl overflow-hidden">
        <div class="border-b border-gray-700">
            <nav class="flex space-x-8 px-6">
                <button class="py-4 border-b-2 border-amber-500 text-amber-500 font-semibold" onclick="switchTab('all')">
                    All Videos
                </button>
                <button class="py-4 border-b-2 border-transparent text-gray-400 hover:text-white font-semibold" onclick="switchTab('hero')">
                    Hero Videos
                </button>
                <button class="py-4 border-b-2 border-transparent text-gray-400 hover:text-white font-semibold" onclick="switchTab('product')">
                    Product Videos
                </button>
                <button class="py-4 border-b-2 border-transparent text-gray-400 hover:text-white font-semibold" onclick="switchTab('promotional')">
                    Promotional
                </button>
            </nav>
        </div>
        
        <!-- Livewire Video Management Component -->
        <div class="p-6">
            @livewire('video-management')
        </div>
    </div>
</div>

<!-- Add/Edit Video Modal -->
<div id="videoModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-700">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-white" id="videoModalTitle">Upload New Video</h3>
                <button onclick="closeVideoModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        
        <form id="videoForm" class="p-6 space-y-6" enctype="multipart/form-data">
            @csrf
            
            <!-- Video Upload Area -->
            <div>
                <label class="block text-white font-semibold mb-4">Video File</label>
                <div class="border-2 border-dashed border-gray-600 rounded-lg p-8 text-center">
                    <div id="uploadArea">
                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                        <p class="text-gray-400 mb-2 text-lg">Drag & drop your video file or click to select</p>
                        <p class="text-gray-500 text-sm mb-4">Supports MP4, AVI, MOV up to 100MB</p>
                        <input type="file" name="video" accept="video/*" class="hidden" id="videoUpload">
                        <button type="button" onclick="document.getElementById('videoUpload').click()" 
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200">
                            Select Video File
                        </button>
                    </div>
                    <div id="videoPreview" class="hidden">
                        <video class="w-full max-w-md mx-auto rounded-lg" controls></video>
                        <button type="button" onclick="removeVideo()" class="mt-4 text-red-400 hover:text-red-300">
                            <i class="fas fa-trash mr-2"></i>Remove Video
                        </button>
                    </div>
                    <div id="uploadProgress" class="hidden">
                        <div class="w-full bg-gray-700 rounded-full h-2 mb-4">
                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p class="text-gray-400">Uploading...</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white border-b border-gray-700 pb-2">Video Details</h4>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Video Title</label>
                        <input type="text" name="title" required
                               placeholder="Enter video title..."
                               class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-purple-500 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Video Type</label>
                        <select name="type" required
                                class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-purple-500 focus:outline-none">
                            <option value="">Select Type</option>
                            <option value="hero">Hero Video</option>
                            <option value="product">Product Video</option>
                            <option value="promotional">Promotional</option>
                            <option value="testimonial">Testimonial</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Duration (seconds)</label>
                        <input type="number" name="duration" min="1"
                               placeholder="Video duration in seconds"
                               class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-purple-500 focus:outline-none">
                    </div>
                </div>
                
                <!-- Settings & Options -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white border-b border-gray-700 pb-2">Settings</h4>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Thumbnail</label>
                        <input type="file" name="thumbnail" accept="image/*"
                               class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-purple-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white">
                        <p class="text-gray-500 text-sm mt-1">Optional: Upload custom thumbnail</p>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Associated Product</label>
                        <select name="product_id"
                                class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-purple-500 focus:outline-none">
                            <option value="">No Product Association</option>
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="autoplay" value="1"
                                   class="mr-3 w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                            <span class="text-white">Autoplay (for hero videos)</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="loop" value="1"
                                   class="mr-3 w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                            <span class="text-white">Loop video</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="mr-3 w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                            <span class="text-white">Active video</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div>
                <label class="block text-white font-semibold mb-2">Video Description</label>
                <textarea name="description" rows="3" 
                          placeholder="Enter video description..."
                          class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-purple-500 focus:outline-none"></textarea>
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                <button type="button" onclick="closeVideoModal()" 
                        class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg hover:from-purple-600 hover:to-indigo-700 transition-all duration-300 font-semibold">
                    Upload Video
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openVideoModal(videoId = null) {
        document.getElementById('videoModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        if (videoId) {
            document.getElementById('videoModalTitle').textContent = 'Edit Video';
            // Load video data via AJAX
        } else {
            document.getElementById('videoModalTitle').textContent = 'Upload New Video';
            document.getElementById('videoForm').reset();
        }
    }
    
    function closeVideoModal() {
        document.getElementById('videoModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        removeVideo();
    }
    
    function switchTab(tab) {
        // Update tab styling
        const tabs = document.querySelectorAll('nav button');
        tabs.forEach(t => {
            t.className = 'py-4 border-b-2 border-transparent text-gray-400 hover:text-white font-semibold';
        });
        event.target.className = 'py-4 border-b-2 border-amber-500 text-amber-500 font-semibold';
        
        // Filter videos via Livewire
        Livewire.emit('filterByType', tab);
    }
    
    // Video upload functionality
    document.getElementById('videoUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const video = document.querySelector('#videoPreview video');
            video.src = URL.createObjectURL(file);
            document.getElementById('uploadArea').classList.add('hidden');
            document.getElementById('videoPreview').classList.remove('hidden');
        }
    });
    
    function removeVideo() {
        document.getElementById('videoUpload').value = '';
        document.getElementById('uploadArea').classList.remove('hidden');
        document.getElementById('videoPreview').classList.add('hidden');
        const video = document.querySelector('#videoPreview video');
        if (video.src) {
            URL.revokeObjectURL(video.src);
            video.src = '';
        }
    }
</script>
@endsection