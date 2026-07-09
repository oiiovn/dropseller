<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
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
            'order_id' => ['required', 'exists:crm_orders,id'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'delivery_status' => ['nullable', 'in:pending,ready,shipping,delivered,failed,cancelled'],
            'shipping_fee_jpy' => ['nullable', 'numeric', 'min:0'],
            'delivered_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }
}
