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
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this course?')">
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
                    @if ($course->thumbnail)
                        <div class="course-thumbnail mb-3">
                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('images/default-thumbnail.png') }}"
                                alt="Course Thumbnail" class="img-fluid rounded">
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
                            @if ($module->contents->isEmpty())
                                <div class="alert alert-info mb-0">
                                    This module doesn't have any contents yet.
                                </div>
                            @else
                                <div class="list-group">
                                    @foreach ($module->contents as $content)
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
                                                    @if ($content->description)
                                                        <small class="text-muted">{{ $content->description }}</small>
                                                    @endif

                                                    @if (in_array($content->type, ['image', 'video', 'file']))
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                File: {{ $content->original_filename }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if ($content->type === 'image')
                                                        <a href="{{ $content->content ? asset('storage/' . $content->content) : asset('images/default-thumbnail.png') }}"
                                                            target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye mr-1"></i> View Image
                                                        </a>
                                                    @elseif($content->type === 'file')
                                                        <a href="{{ asset('storage/' . $content->content) }}"
                                                            class="btn btn-sm btn-primary" download>
                                                            <i class="fas fa-download mr-1"></i> Download
                                                        </a>
                                                    @elseif($content->type === 'link')
                                                        <a href="{{ $content->content }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-external-link-alt mr-1"></i> Visit Link
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Content Display Area -->
                                            <div class="content-display mt-3">
                                                @if($content->type === 'text')
                                                    <div class="text-content p-3 bg-light rounded">
                                                        {!! nl2br(e($content->content)) !!}
                                                    </div>
                                                @elseif($content->type === 'video')
                                                    <div class="video-container mt-2">
                                                        <video controls class="w-100" style="max-height: 400px;">
                                                            <source src="{{ asset('storage/' . $content->content) }}" type="video/mp4">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    </div>
                                                @endif
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

        .video-container {
            background-color: #000;
            border-radius: 5px;
            overflow: hidden;
        }

        .text-content {
            white-space: pre-wrap;
            border: 1px solid #dee2e6;
        }
    </style>
@endpush