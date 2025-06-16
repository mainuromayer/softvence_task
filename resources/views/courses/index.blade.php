@extends('app')

@section('title', 'All Courses')

@section('content')
    <div class="card card-primary mt-3">
        <div class="card-header">
            <h3 class="card-title">All Courses</h3>
            <div class="card-tools">
                <a href="{{ route('courses.create') }}" class="btn btn-sm btn-light text-dark">
                    <i class="fas fa-plus"></i> Create New
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Modules</th>
                        <th>Contents</th>
                        <th style="width: 150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $course->title }}</td>
                        <td>{{ $course->category }}</td>
                        <td>{{ $course->modules_count }}</td>
                        <td>{{ $course->contents_through_modules_count }}</td>
                        <td>
                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $courses->links() }}
        </div>
    </div>
@endsection