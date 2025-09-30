<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Video;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class VideoManagement extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $editingVideo = null;
    
    // Video form fields
    public $title = '';
    public $description = '';
    public $type = 'hero'; // hero, product_intro, category_intro
    public $category_id = '';
    public $video_file = null;
    public $video_url = '';
    public $thumbnail = null;
    public $is_active = true;
    public $order = 0;
    
    // Filters
    public $typeFilter = '';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:500',
        'type' => 'required|in:hero,product_intro,category_intro',
        'category_id' => 'nullable|exists:categories,id',
        'video_file' => 'nullable|mimes:mp4,avi,mov,webm|max:51200', // 50MB max
        'video_url' => 'nullable|url',
        'thumbnail' => 'nullable|image|max:2048',
        'order' => 'nullable|integer|min:0',
    ];

    public function render()
    {
        $categories = Category::all();
        
        $videosQuery = Video::query()->active();
        
        if ($this->typeFilter) {
            $videosQuery->where('type', $this->typeFilter);
        }
        
        $videos = $videosQuery->orderBy('order', 'asc')->orderBy('updated_at', 'desc')->get();

        return view('livewire.video-management', compact('videos', 'categories'));
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingVideo = null;
        $this->title = '';
        $this->description = '';
        $this->type = 'hero';
        $this->category_id = '';
        $this->video_file = null;
        $this->video_url = '';
        $this->thumbnail = null;
        $this->is_active = true;
        $this->order = 0;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'category_id' => $this->category_id ?: null,
            'video_url' => $this->video_url ?: null,
            'is_active' => $this->is_active,
            'order' => $this->order ?: 0,
        ];

        // Handle video file upload
        if ($this->video_file) {
            $videoPath = $this->video_file->store('videos/' . $this->type, 'public');
            $data['video_file'] = $videoPath;
            
            // Get video metadata
            $data['file_size'] = $this->video_file->getSize();
        }

        // Handle thumbnail upload
        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('thumbnails', 'public');
            $data['thumbnail'] = $thumbnailPath;
        }

        if ($this->editingVideo) {
            $video = Video::findOrFail($this->editingVideo);
            $video->update($data);
            session()->flash('message', 'Video updated successfully!');
        } else {
            Video::create($data);
            session()->flash('message', 'Video created successfully!');
        }

        $this->closeModal();
    }

    public function edit($videoId)
    {
        $video = Video::findOrFail($videoId);
        
        $this->editingVideo = $videoId;
        $this->title = $video->title;
        $this->description = $video->description;
        $this->type = $video->type;
        $this->category_id = $video->category_id;
        $this->video_url = $video->video_url;
        $this->is_active = $video->is_active;
        $this->order = $video->order;
        
        $this->openModal();
    }

    public function delete($videoId)
    {
        $video = Video::findOrFail($videoId);
        
        // Delete associated files
        if ($video->video_file) {
            Storage::disk('public')->delete($video->video_file);
        }
        
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        
        $video->delete();
        
        session()->flash('message', 'Video deleted successfully!');
    }

    public function toggleStatus($videoId)
    {
        $video = Video::findOrFail($videoId);
        $video->update(['is_active' => !$video->is_active]);
        
        session()->flash('message', 'Video status updated!');
    }

    public function setAsHeroVideo($videoId)
    {
        // Set all hero videos as inactive first
        Video::where('type', 'hero')->update(['is_active' => false]);
        
        // Set this video as active hero video
        $video = Video::findOrFail($videoId);
        $video->update(['is_active' => true, 'type' => 'hero']);
        
        session()->flash('message', 'Hero video updated!');
    }
}
