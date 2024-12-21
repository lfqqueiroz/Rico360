<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeCallRequest extends FormRequest
{
    // Determines if the user is authorized to make this request
    public function authorize()
    {
        return true; // Change as needed, for example to check user permissions
    }

    // Defines the validation rules for the request
    public function rules()
    {
        return [
            'receiver_id' => ['required', 'exists:users,id'],
        ];
    }

    // Custom error messages for validation failures
    public function messages()
    {
        return [
            'receiver_id.required' => 'The receiver ID field is required.',
            'receiver_id.exists' => 'The selected user was not found.',
        ];
    }
}
