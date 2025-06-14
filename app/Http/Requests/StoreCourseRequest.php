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
            'category' => 'required|string|max:100',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.contents.*.title' => 'required|string|max:255',
            'modules.*.contents.*.type' => 'required|in:text,image,video,file,link',
            'modules.*.contents.*.content' => 'required',
            'modules.*.contents.*.description' => 'nullable|string',
        ];
    }
}
