<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'poll_option_id' => 'required|integer|exists:poll_options,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'poll_option_id.required' => 'Please select an option to vote',
            'poll_option_id.integer' => 'Invalid option selected',
            'poll_option_id.exists' => 'Selected option does not exist',
        ];
    }
}
