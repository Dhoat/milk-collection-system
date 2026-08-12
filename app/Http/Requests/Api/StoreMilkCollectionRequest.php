<?php

namespace App\Http\Requests\Api;

use App\Models\MilkCollection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMilkCollectionRequest extends FormRequest
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
            'farmer_id' => [
                'required',
                Rule::exists('farmers', 'id')->where('status', true),
            ],
            'collection_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:morning,evening'],
            'milk_quantity' => ['required', 'numeric', 'gt:0'],
            'fat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'snf' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rate' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure additional validator callbacks for duplicate collection prevention.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled(['farmer_id', 'collection_date', 'shift'])) {
                $exists = MilkCollection::where('farmer_id', $this->input('farmer_id'))
                    ->whereDate('collection_date', $this->input('collection_date'))
                    ->where('shift', $this->input('shift'))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'farmer_id',
                        'A milk collection entry already exists for this farmer on the selected date and shift.'
                    );
                }
            }
        });
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'farmer_id.required' => 'Please select a valid active farmer.',
            'farmer_id.exists' => 'The selected farmer does not exist or is inactive.',
            'collection_date.required' => 'The collection date is required.',
            'shift.in' => 'The shift must be either morning or evening.',
            'milk_quantity.gt' => 'Milk quantity must be greater than zero.',
        ];
    }
}
