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
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            
            'modules.*.contents' => 'required|array|min:1',
            'modules.*.contents.*.title' => 'required|string|max:255',
            'modules.*.contents.*.type' => 'required|in:text,image,video,file,link',
            'modules.*.contents.*.description' => 'nullable|string',
            
            // Conditional validation based on content type
            'modules.*.contents.*.content' => 'required_if:modules.*.contents.*.type,text,link',
            'modules.*.contents.*.content_file' => 'required_if:modules.*.contents.*.type,image,video,file|nullable|file',
        ];
    }

    public function messages()
{
    return [
        'modules.required' => 'Each course must have at least one module.',
        'modules.*.contents.required' => 'Each module must have at least one content item.',
        'modules.*.contents.min' => 'Each module must have at least one content item.',
        'modules.*.contents.*.content.required_if' => 'The content field is required for this type.',
        'modules.*.contents.*.content_file.required_if' => 'Please upload a file for this content type.',
    ];
}
}