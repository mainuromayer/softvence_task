@extends('app')

@section('title', $course->title)

@section('content')
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">{{ $course->title }}</h3>
                <div>
                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-light mr-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Course Overview Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    @if($course->thumbnail)
                        <div class="course-thumbnail mb-3">
                            <img src="{{ Storage::url($course->thumbnail) }}" alt="Course Thumbnail" class="img-fluid rounded">
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Course Details</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Category:</strong> {{ $course->category }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Modules:</strong> {{ $course->modules->count() }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Contents:</strong> {{ $course->modules->sum('contents_count') }}
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Description</h5>
                        </div>
                        <div class="card-body">
                            {!! $course->description ? nl2br(e($course->description)) : '<p class="text-muted">No description provided</p>' !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modules and Contents Section -->
            <div class="course-modules">
                <h4 class="mb-3 border-bottom pb-2">Course Modules</h4>
                
                @forelse($course->modules as $module)
                    <div class="card module-card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                Module {{ $loop->iteration }}: {{ $module->title }}
                                <span class="badge badge-pill badge-primary float-right">
                                    {{ $module->contents_count }} {{ Str::plural('Content', $module->contents_count) }}
                                </span>
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            @if($module->contents->isEmpty())
                                <div class="alert alert-info mb-0">
                                    This module doesn't have any contents yet.
                                </div>
                            @else
                                <div class="list-group">
                                    @foreach($module->contents as $content)
                                        <div class="list-group-item content-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="fas 
                                                            @switch($content->type)
                                                                @case('text') fa-file-alt @break
                                                                @case('image') fa-image @break
                                                                @case('video') fa-video @break
                                                                @case('file') fa-file-download @break
                                                                @case('link') fa-link @break
                                                                @default fa-question-circle
                                                            @endswitch
                                                            mr-2"></i>
                                                        {{ $content->title }}
                                                    </h6>
                                                    @if($content->description)
                                                        <small class="text-muted">{{ $content->description }}</small>
                                                    @endif
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-primary view-content" 
                                                            data-content-id="{{ $content->id }}"
                                                            data-content-type="{{ $content->type }}">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        This course doesn't have any modules yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Content Viewer Modal -->
    <div class="modal fade" id="contentViewerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contentModalTitle">Content Viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contentModalBody">
                    <!-- Content will be loaded here dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .course-thumbnail {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        overflow: hidden;
        background-color: #f8f9fa;
        padding: 5px;
    }
    
    .content-item {
        transition: background-color 0.2s;
    }
    
    .content-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle content viewing
    $('.view-content').click(function() {
        const contentId = $(this).data('content-id');
        const contentType = $(this).data('content-type');
        
        // Show loading state
        $('#contentModalTitle').text('Loading...');
        $('#contentModalBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading content...</p>
            </div>
        `);
        
        $('#contentViewerModal').modal('show');
        
        // Load content via AJAX
        $.get(`/contents/${contentId}/view`, function(response) {
            $('#contentModalTitle').text(response.title);
            
            let contentHtml = '';
            if (contentType === 'text') {
                contentHtml = `<div class="content-text">${response.content}</div>`;
            } 
            else if (contentType === 'image') {
                contentHtml = `
                    <div class="text-center">
                        <img src="${response.content_url}" class="img-fluid" alt="${response.title}">
                    </div>
                `;
            }
            else if (contentType === 'video') {
                contentHtml = `
                    <div class="embed-responsive embed-responsive-16by9">
                        <video controls class="embed-responsive-item">
                            <source src="${response.content_url}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                `;
            }
            else if (contentType === 'file') {
                contentHtml = `
                    <div class="text-center">
                        <a href="${response.content_url}" class="btn btn-primary" download>
                            <i class="fas fa-download mr-2"></i> Download File
                        </a>
                        <p class="mt-3">${response.description || ''}</p>
                    </div>
                `;
            }
            else if (contentType === 'link') {
                contentHtml = `
                    <div class="text-center">
                        <a href="${response.content}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt mr-2"></i> Visit Link
                        </a>
                        <p class="mt-3">${response.description || ''}</p>
                    </div>
                `;
            }
            
            $('#contentModalBody').html(contentHtml);
        }).fail(function() {
            $('#contentModalBody').html(`
                <div class="alert alert-danger">
                    Failed to load content. Please try again.
                </div>
            `);
        });
    });
});
</script>
@endpush