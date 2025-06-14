@extends('app')

@section('title', 'Courses')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Courses</h3>
            <a href="{{ route('courses.create') }}" class="btn btn-primary float-right">Create New Course</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <table class="table table-bordered" id="courses-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Modules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr id="course-{{ $course->id }}">
                            <td>{{ $course->id }}</td>
                            <td>
                                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" 
                                     style="max-width: 100px; max-height: 60px;" class="img-thumbnail">
                            </td>
                            <td>{{ $course->title }}</td>
                            <td>{{ $course->category }}</td>
                            <td>{{ $course->modules_count }}</td>
                            <td>
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No courses found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $courses->links() }}
        </div>
    </div>
@endsection