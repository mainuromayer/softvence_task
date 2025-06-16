<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'thumbnail' => 'nullable|image',
            
            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            
            'modules.*.contents' => 'required|array|min:1',
            'modules.*.contents.*.title' => 'required|string|max:255',
            'modules.*.contents.*.type' => 'required|string|in:text,image,video,file,link',
            'modules.*.contents.*.description' => 'nullable|string',
            
            'modules.*.contents.*.content' => 'required_if:modules.*.contents.*.type,text,link',
            'modules.*.contents.*.content_file' => 'required_if:modules.*.contents.*.type,image,video,file|file',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The course title is required.',
            'category.required' => 'Please select a category for the course.',
            'thumbnail.image' => 'The thumbnail must be an image file.',
            
            'modules.required' => 'At least one module is required.',
            'modules.min' => 'At least one module is required.',
            'modules.*.title.required' => 'Each module must have a title.',
            
            'modules.*.contents.required' => 'Each module must have at least one content item.',
            'modules.*.contents.min' => 'Each module must have at least one content item.',
            'modules.*.contents.*.title.required' => 'Each content item must have a title.',
            'modules.*.contents.*.type.required' => 'Please select a content type.',
            'modules.*.contents.*.type.in' => 'Invalid content type selected.',
            
            'modules.*.contents.*.content.required_if' => 'Content is required for this type.',
            'modules.*.contents.*.content_file.required_if' => 'Please upload a file for this content type.',
            'modules.*.contents.*.content_file.file' => 'The uploaded file is invalid.',
            
        ];
    }
}