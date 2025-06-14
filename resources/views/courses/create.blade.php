@extends('app')

@section('title', 'Create Course')

@section('content')
    <div class="card card-primary mt-3">
        <div class="card-header">
            <h3 class="card-title">Create New Course</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
                @csrf

                <div class="course-section border-bottom pb-3 mb-3">
                    <h4 class="mb-3">Course Information</h4>
                    <div class="form-group">
                        <label for="title">Course Title*</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category*</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Programming">Programming</option>
                                    <option value="Design">Design</option>
                                    <option value="Business">Business</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Personal Development">Personal Development</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="thumbnail">Thumbnail</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="thumbnail" name="thumbnail">
                                    <label class="custom-file-label" for="thumbnail">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modules-section">
                    <h4 class="mb-3">Modules</h4>
                    <div id="modules-container">
                        <!-- First module will be added by default -->
                    </div>
                    <button type="button" class="btn btn-primary add-module-btn" id="add-module">
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

            // Add first module by default (without delete button)
            addModule(true);

            // Add Module
            $('#add-module').click(function() {
                addModule(false);
            });

            function addModule(isFirstModule) {
                moduleCount++;
                contentCounters[moduleCount] = 0;

                const deleteButton = isFirstModule ? '' :
                    `<button type="button" class="btn btn-sm btn-danger remove-module position-absolute" style="right: 0; top: 0;">
                        <i class="fas fa-trash"></i>
                    </button>`;

                const moduleHtml = `
                <div class="module-container position-relative mb-4">
                    <div class="module-item card card-primary card-outline mt-3 text-dark" data-module-index="${moduleCount}">
                        <div class="card-header">
                            <div class="card-header bg-light pointer" data-toggle="collapse" 
     data-target="#module-collapse-${moduleCount}" aria-expanded="true">
    <h5 class="mb-0 d-flex justify-content-between align-items-center">
        <span>Module #${moduleCount}</span>
        
    </h5>
</div>

                        </div>
                        <div id="module-collapse-${moduleCount}" class="collapse show">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Module Title*</label>
                                    <input type="text" class="form-control" name="modules[${moduleCount}][title]" required>
                                </div>
                                <div class="contents-container mb-3">
                                    <label>Contents</label>
                                    <div class="contents-list" id="contents-${moduleCount}">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-info add-content" data-module="${moduleCount}">
                                        <i class="fas fa-plus mr-1"></i> Add Content
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${deleteButton}
                </div>`;

                $('#modules-container').append(moduleHtml);
                addContent(moduleCount);
            }

            // Add Content to Module
            $(document).on('click', '.add-content', function() {
                const moduleIndex = $(this).data('module');
                addContent(moduleIndex);
            });

            function addContent(moduleIndex) {
                contentCounters[moduleIndex] = (contentCounters[moduleIndex] || 0) + 1;
                const contentIndex = contentCounters[moduleIndex];

                const isFirstContent = contentIndex === 1;

                const deleteButton = isFirstContent ? '' : `
        <button type="button" class="btn btn-sm btn-danger remove-content position-absolute" style="right: 0; top: 0;">
            <i class="fas fa-trash"></i>
        </button>`;

                const contentHtml = `
    <div class="content-container position-relative mb-3">
        <div class="content-item card card-primary card-outline mt-3 text-dark" data-content-index="${contentIndex}">
            <div class="card-header bg-light pointer" data-toggle="collapse" 
                 data-target="#content-collapse-${moduleIndex}-${contentIndex}" aria-expanded="true">
                <h6 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>Content #${contentIndex}</span>
                    
                </h6>
            </div>
            <div id="content-collapse-${moduleIndex}-${contentIndex}" class="collapse show">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Content Title*</label>
                                <input type="text" class="form-control" 
                                    name="modules[${moduleIndex}][contents][${contentIndex}][title]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Type*</label>
                                <select class="form-control content-type" 
                                        name="modules[${moduleIndex}][contents][${contentIndex}][type]" required>
                                    <option value="text">Text</option>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="file">File</option>
                                    <option value="link">Link</option>
                                </select>
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
                    <div class="form-group content-field">
                        <label>Content*</label>
                        <textarea class="form-control" 
                                name="modules[${moduleIndex}][contents][${contentIndex}][content]" required></textarea>
                    </div>
                </div>
            </div>
        </div>
        ${deleteButton}
    </div>`;

                $(`#contents-${moduleIndex}`).append(contentHtml);
            }

            // Remove Module
            $(document).on('click', '.remove-module', function() {
                $(this).closest('.module-container').remove();
                if ($('.module-item').length === 0) {
                    moduleCount = 0;
                }
            });

            // Remove Content
            $(document).on('click', '.remove-content', function() {
                $(this).closest('.content-container').remove();
            });

            // Change content field based on type
            $(document).on('change', '.content-type', function() {
                const type = $(this).val();
                const contentField = $(this).closest('.card-body').find('.content-field');
                const moduleIndex = $(this).closest('.module-item').data('module-index');
                const contentIndex = $(this).closest('.content-item').data('content-index');

                if (type === 'image' || type === 'file' || type === 'video') {
                    contentField.html(`
                        <label>Content*</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" 
                                   name="modules[${moduleIndex}][contents][${contentIndex}][content]" required>
                            <label class="custom-file-label">Choose file</label>
                        </div>
                    `);
                } else {
                    contentField.html(`
                        <label>Content*</label>
                        <textarea class="form-control" 
                                name="modules[${moduleIndex}][contents][${contentIndex}][content]" required></textarea>
                    `);
                }
            });

            // BS custom file input
            $(document).on('change', '.custom-file-input', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .module-container,
        .content-container {
            position: relative;
            padding-right: 40px;
            /* Space for delete button */
        }

        .module-item,
        .content-item {
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .module-item .card-header,
        .content-item .card-header {
            background-color: #f8f9fa;
            padding: 0.75rem 1.25rem;
        }

        .module-item .btn-link,
        .content-item .btn-link {
            color: #495057;
            text-decoration: none;
            width: 100%;
            text-align: left;
            padding: 0;
        }

        .module-item .btn-link:hover,
        .content-item .btn-link:hover {
            text-decoration: none;
        }

        .module-item .btn-link:focus,
        .content-item .btn-link:focus {
            text-decoration: none;
            box-shadow: none;
        }

        .remove-module,
        .remove-content {
            position: absolute;
            right: 0;
            top: 0;
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .add-module-btn,
        .add-content {
            margin-top: 10px;
        }

        .content-field {
            margin-top: 15px;
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .collapse.show {
            display: block;
        }

        .collapse-icon {
            transition: transform 0.3s ease;
        }

        .collapsed .collapse-icon {
            transform: rotate(180deg);
        }
    </style>
@endpush
