<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled via Gate in Controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2000|max:2099',
            'village_id' => 'nullable|exists:villages,id',
            'shop_id' => 'nullable|exists:shops,id',
            'product_id' => 'nullable|exists:products,id',
        ];
    }
}
