<?php

namespace App\Http\Requests\Crm;

use App\Support\Crm\CollaboratorScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()?->hasCrmRole('collaborator')) {
            $this->merge([
                'affiliate_id' => CollaboratorScope::affiliateId($this->user()),
            ]);
        }

        if ($this->filled('product_name') && ! $this->has('items')) {
            $bikePrice = (float) $this->input('bike_price_jpy', 0);

            $this->merge([
                'items' => [[
                    'product_name' => $this->input('product_name'),
                    'quantity' => 1,
                    'unit_price_jpy' => $bikePrice,
                    'unit_cost_jpy' => 0,
                ]],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'affiliate_id' => [$this->user()?->hasCrmRole('collaborator') ? 'nullable' : 'required', 'exists:affiliates,id'],
            'order_status' => ['nullable', 'in:new,consulting,confirmed,deposit_pending,deposit_paid,waiting_delivery,packaged,delivering,delivered,completed,cancelled,refunded'],
            'discount_jpy' => ['nullable', 'numeric', 'min:0'],
            'shipping_fee_jpy' => ['nullable', 'numeric', 'min:0'],
            'debt_due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'battery_capacity' => ['nullable', 'string', 'max:100'],
            'bike_price_jpy' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'shipping_mode' => ['nullable', 'in:boxed,direct_ship'],
            'delivery_date' => ['nullable', 'date'],
            'delivery_date_from' => ['nullable', 'date'],
            'delivery_date_to' => ['nullable', 'date', 'after_or_equal:delivery_date_from'],
            'delivery_time_slot' => ['nullable', 'string', 'max:120'],
            'confirmed_at' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['nullable', 'string', 'max:255'],
            'items.*.product_id' => ['nullable', 'exists:crm_products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price_jpy' => ['required', 'numeric', 'min:0'],
            'items.*.unit_cost_jpy' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('items', []) as $index => $item) {
                if (empty($item['product_id']) && empty($item['product_name'])) {
                    $validator->errors()->add("items.$index.product_name", 'Vui lòng nhập tên sản phẩm.');
                }
            }
        });
    }
}
