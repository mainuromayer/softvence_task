@extends('app')

@section('title', $course->title)

@section('content')
    <div class="card" id="course-details">
        <div class="card-header">
            <h3 class="card-title">Course Details</h3>
            <a href="{{ route('courses.index') }}" class="btn btn-primary float-right">Back to Courses</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" 
                         class="img-fluid img-thumbnail" id="course-thumbnail">
                </div>
                <div class="col-md-9">
                    <h2 id="course-title">{{ $course->title }}</h2>
                    <p><strong>Category:</strong> <span id="course-category">{{ $course->category }}</span></p>
                    <p><strong>Description:</strong></p>
                    <div id="course-description">{!! nl2br(e($course->description)) !!}</div>
                </div>
            </div>

            <hr>

            <h4 class="mt-4">Modules</h4>
            <div class="accordion" id="modules-accordion">
                @foreach($course->modules as $module)
                    <div class="card module-item" id="module-{{ $module->id }}">
                        <div class="card-header" id="heading-{{ $module->id }}">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" 
                                        data-target="#collapse-{{ $module->id }}" 
                                        aria-expanded="true" aria-controls="collapse-{{ $module->id }}">
                                    {{ $module->title }}
                                </button>
                            </h5>
                        </div>

                        <div id="collapse-{{ $module->id }}" class="collapse" 
                             aria-labelledby="heading-{{ $module->id }}" data-parent="#modules-accordion">
                            <div class="card-body">
                                @foreach($module->contents as $content)
                                    <div class="content-item mb-3 p-3 border rounded" id="content-{{ $content->id }}">
                                        <h5>{{ $content->title }}</h5>
                                        <p><strong>Type:</strong> {{ ucfirst($content->type) }}</p>
                                        
                                        @if($content->description)
                                            <p><strong>Description:</strong> {{ $content->description }}</p>
                                        @endif

                                        <div class="content-display mt-2">
                                            @if($content->type === 'text')
                                                <div class="p-2 bg-light rounded">
                                                    {!! nl2br(e($content->content)) !!}
                                                </div>
                                            @elseif($content->type === 'image')
                                                <img src="{{ Storage::url($content->content) }}" 
                                                     alt="{{ $content->title }}" class="img-fluid">
                                            @elseif($content->type === 'video')
                                                <video controls class="w-100">
                                                    <source src="{{ Storage::url($content->content) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @elseif($content->type === 'file')
                                                <a href="{{ Storage::url($content->content) }}" 
                                                   class="btn btn-primary" download>
                                                    Download File
                                                </a>
                                            @elseif($content->type === 'link')
                                                <a href="{{ $content->content }}" target="_blank" 
                                                   class="btn btn-info">
                                                    Visit Link
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #course-thumbnail {
            max-width: 100%;
            height: auto;
        }
        
        .module-item {
            margin-bottom: 10px;
        }
        
        .content-item {
            background-color: #f8f9fa;
        }
    </style>
@endpush