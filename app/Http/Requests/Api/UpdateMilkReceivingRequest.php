<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMilkReceivingRequest extends FormRequest
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
        $receiving = $this->route('milkReceiving') ?? $this->route('milk_receiving');
        $receivingId = is_object($receiving) ? $receiving->id : $receiving;

        return [
            'village_id' => [
                'required',
                'exists:villages,id',
                Rule::unique('milk_receivings')->where(function ($query) {
                    return $query->where('receiving_date', $this->input('receiving_date'))
                                 ->where('shift', $this->input('shift'));
                })->ignore($receivingId),
            ],
            'receiving_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:morning,evening'],
            'received_quantity' => ['required', 'numeric', 'min:0'],
            'received_fat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'received_snf' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'village_id.unique' => 'A receiving record already exists for this village, date, and shift.',
            'receiving_date.required' => 'The receiving date is required.',
            'shift.in' => 'The shift must be either morning or evening.',
            'received_quantity.min' => 'Received quantity must be zero or greater.',
        ];
    }
}
