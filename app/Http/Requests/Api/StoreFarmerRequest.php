<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerRequest extends FormRequest
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
            'village_id' => ['required', 'exists:villages,id'],
            'farmer_code' => ['required', 'string', 'max:50', 'unique:farmers,farmer_code'],
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9+() -]{10,15}$/'],
            'alternate_mobile' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^[0-9+() -]{10,15}$/'],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'joining_date' => ['nullable', 'date'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'village_id.required' => 'The village selection is required.',
            'village_id.exists' => 'The selected village does not exist.',
            'farmer_code.required' => 'The farmer code is required.',
            'farmer_code.unique' => 'The farmer code has already been taken.',
            'mobile.regex' => 'Please enter a valid mobile number.',
        ];
    }
}
