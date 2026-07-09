<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommissionPayoutRequest extends FormRequest
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
            'amount_jpy' => ['required', 'numeric', 'min:1'],
            'paid_at' => ['required', 'date'],
            'payment_method' => ['nullable', 'in:bank_transfer,cash,card'],
            'reference_code' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string'],
        ];
    }
}
