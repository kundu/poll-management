<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreatePollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Will be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'allow_guest_voting' => 'required|boolean',
            'options' => 'required|array|min:2|max:10',
            'options.*.option_text' => 'required|string|max:200',
            'options.*.order_index' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Poll title is required.',
            'title.max' => 'Poll title cannot exceed 100 characters.',
            'description.max' => 'Poll description cannot exceed 500 characters.',
            'allow_guest_voting.required' => 'Guest voting setting is required.',
            'options.required' => 'Poll options are required.',
            'options.min' => 'At least 2 options are required.',
            'options.max' => 'Maximum 10 options are allowed.',
            'options.*.option_text.required' => 'Option text is required.',
            'options.*.option_text.max' => 'Option text cannot exceed 200 characters.',
            'options.*.order_index.required' => 'Option order is required.',
            'options.*.order_index.integer' => 'Option order must be a number.',
            'options.*.order_index.min' => 'Option order must be 0 or greater.',
        ];
    }
}
