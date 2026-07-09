<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['sometimes', 'exists:crm_orders,id'],
            'customer_id' => ['sometimes', 'exists:customers,id'],
            'amount_jpy' => ['required', 'numeric', 'min:1'],
            'discount_jpy' => ['nullable', 'numeric', 'min:0'],
            'payment_type' => ['required', 'in:deposit,partial,full'],
            'payment_method' => ['nullable', 'in:bank_transfer,cash,card,installment,other'],
            'payment_date' => ['required', 'date'],
            'reference_code' => ['nullable', 'string', 'max:120'],
            'payment_status' => ['nullable', 'in:received,pending,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
