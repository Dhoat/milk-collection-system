<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled via Policy in Controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shop_code' => 'required|string|max:50|unique:shops,shop_code',
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'village_id' => 'nullable|exists:villages,id',
            'area' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|boolean',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
