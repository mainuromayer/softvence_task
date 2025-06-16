@extends('app')

@section('title', 'Create Course')

@section('content')
    <div class="card card-primary mt-3">
        <div class="card-header">
            <h3 class="card-title">Create New Course</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
                @csrf

                <div class="course-section border-bottom pb-3 mb-3">
                    <h4 class="mb-3">Course Information</h4>
                    <div class="form-group">
                        <label for="title">Course Title*</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category*</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" >
                                    <option value="">Select Category</option>
                                    <option value="Programming" {{ old('category') == 'Programming' ? 'selected' : '' }}>Programming</option>
                                    <option value="Design" {{ old('category') == 'Design' ? 'selected' : '' }}>Design</option>
                                    <option value="Business" {{ old('category') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Marketing" {{ old('category') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="thumbnail">Thumbnail</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail">
                                    <label class="custom-file-label" for="thumbnail">Choose file</label>
                                </div>
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modules-section">
                    <h4 class="mb-3">Modules</h4>
                    <div id="modules-container">
                        <!-- Modules will be added here -->
                    </div>
                    <button type="button" class="btn btn-primary" id="add-module">
                        <i class="fas fa-plus mr-1"></i> Add Module
                    </button>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Save Course</button>
                    <a href="{{ route('courses.index') }}" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    let moduleCount = 0;
    let contentCounters = {};
    
    // Add first module by default
    addModule();
    
    // Add Module
    $('#add-module').click(function() {
        addModule();
    });
    
    function addModule() {
        moduleCount++;
        contentCounters[moduleCount] = 0;
        
        const moduleHtml = `
<div class="module-container card card-primary card-outline mt-3" id="module-${moduleCount}">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Module #${moduleCount}</h5>
        <button type="button" class="btn btn-sm btn-danger remove-module" data-module="${moduleCount}">
            <i class="fas fa-trash"></i> Remove
        </button>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Module Title*</label>
            <input type="text" class="form-control module-title" 
                   name="modules[${moduleCount}][title]" >
            <div class="invalid-feedback module-title-error">Module title is </div>
        </div>
        <div class="contents-container mb-3">
            <label>Contents</label>
            <div class="contents-list" id="contents-${moduleCount}"></div>
            <button type="button" class="btn btn-sm btn-info add-content" data-module="${moduleCount}">
                <i class="fas fa-plus mr-1"></i> Add Content
            </button>
        </div>
    </div>
</div>`;
        
        $('#modules-container').append(moduleHtml);
        addContent(moduleCount);
    }
    
    // Add Content
    $(document).on('click', '.add-content', function() {
        const moduleIndex = $(this).data('module');
        addContent(moduleIndex);
    });
    
    function addContent(moduleIndex) {
        contentCounters[moduleIndex] = (contentCounters[moduleIndex] || 0) + 1;
        const contentIndex = contentCounters[moduleIndex];
        
        const contentHtml = `
<div class="content-container card card-secondary card-outline mt-2" id="content-${moduleIndex}-${contentIndex}">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0">Content #${contentIndex}</h6>
        <button type="button" class="btn btn-sm btn-danger remove-content" 
                data-module="${moduleIndex}" data-content="${contentIndex}">
            <i class="fas fa-trash"></i> Remove
        </button>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Content Title*</label>
                    <input type="text" class="form-control content-title" 
                           name="modules[${moduleIndex}][contents][${contentIndex}][title]" >
                    <div class="invalid-feedback content-title-error">Content title is </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Type*</label>
                    <select class="form-control content-type" 
                            name="modules[${moduleIndex}][contents][${contentIndex}][type]" >
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="file">File</option>
                        <option value="link">Link</option>
                    </select>
                    <div class="invalid-feedback content-type-error">Content type is </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" 
                            name="modules[${moduleIndex}][contents][${contentIndex}][description]"></textarea>
                </div>
            </div>
        </div>
        <div class="form-group content-value-container">
            <label>Content*</label>
            <textarea class="form-control content-value" 
                      name="modules[${moduleIndex}][contents][${contentIndex}][content]" ></textarea>
            <div class="invalid-feedback content-value-error">Content is </div>
        </div>
    </div>
</div>`;
        
        $(`#contents-${moduleIndex}`).append(contentHtml);
        
        // Handle content type change
        $(`#content-${moduleIndex}-${contentIndex} .content-type`).change(function() {
            const type = $(this).val();
            const container = $(this).closest('.card-body').find('.content-value-container');
            
            if (type === 'image' || type === 'file' || type === 'video') {
                container.html(`
                    <label>Content*</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input content-value" 
                               name="modules[${moduleIndex}][contents][${contentIndex}][content_file]" >
                        <label class="custom-file-label">Choose file</label>
                        <div class="invalid-feedback content-value-error">Please upload a file</div>
                    </div>
                `);
            } else {
                container.html(`
                    <label>Content*</label>
                    <textarea class="form-control content-value" 
                            name="modules[${moduleIndex}][contents][${contentIndex}][content]" ></textarea>
                    <div class="invalid-feedback content-value-error">Content is </div>
                `);
            }
        });
    }
    
    // Remove Module
    $(document).on('click', '.remove-module', function() {
        const moduleId = $(this).data('module');
        $(`#module-${moduleId}`).remove();
        renumberModules();
    });
    
    // Remove Content
    $(document).on('click', '.remove-content', function() {
        const moduleId = $(this).data('module');
        const contentId = $(this).data('content');
        $(`#content-${moduleId}-${contentId}`).remove();
    });
    
    // Renumber modules after deletion
    function renumberModules() {
        $('.module-container').each(function(index) {
            const newIndex = index + 1;
            $(this).attr('id', `module-${newIndex}`);
            $(this).find('.card-header h5').text(`Module #${newIndex}`);
            $(this).find('.remove-module').data('module', newIndex);
            
            // Update all names in the module
            $(this).find('[name^="modules["]').each(function() {
                const name = $(this).attr('name').replace(/modules\[\d+\]/, `modules[${newIndex}]`);
                $(this).attr('name', name);
            });
            
            // Update content buttons
            $(this).find('.add-content').data('module', newIndex);
        });
    }
    
    // BS custom file input
    $(document).on('change', '.custom-file-input', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Handle form validation errors
    @if($errors->any())
        @foreach($errors->getMessages() as $field => $messages)
            @if(str_starts_with($field, 'modules.'))
                @php
                    $parts = explode('.', $field);
                    $moduleIdx = $parts[1];
                    $fieldName = $parts[2];
                    
                    if (count($parts) > 3) {
                        // Content error
                        $contentIdx = $parts[3];
                        $contentField = $parts[4];
                @endphp
                $(function() {
                    const input = $('input[name="modules[{{ $moduleIdx }}][contents][{{ $contentIdx }}][{{ $contentField }}]"], ' + 
                                 'select[name="modules[{{ $moduleIdx }}][contents][{{ $contentIdx }}][{{ $contentField }}]"], ' +
                                 'textarea[name="modules[{{ $moduleIdx }}][contents][{{ $contentIdx }}][{{ $contentField }}]"]');
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text('{{ $messages[0] }}').show();
                });
                @php
                    } else {
                        // Module error
                @endphp
                $(function() {
                    const input = $('input[name="modules[{{ $moduleIdx }}][{{ $fieldName }}]"], ' + 
                                 'select[name="modules[{{ $moduleIdx }}][{{ $fieldName }}]"], ' +
                                 'textarea[name="modules[{{ $moduleIdx }}][{{ $fieldName }}]"]');
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text('{{ $messages[0] }}').show();
                });
                @php
                    }
                @endphp
            @endif
        @endforeach
    @endif
});
</script>
@endpush