<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMilkStockOutRequest extends FormRequest
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
            'transaction_date' => 'required|date',
            'quantity' => 'required|numeric|gt:0',
            'source_or_reason' => 'required|string|max:255',
            'fat' => 'nullable|numeric|min:0|max:100',
            'snf' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
