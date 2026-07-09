<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'sku' => ['required', 'string', 'max:80', 'unique:crm_products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'base_cost_jpy' => ['required', 'numeric', 'min:0'],
            'recommended_price_jpy' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
