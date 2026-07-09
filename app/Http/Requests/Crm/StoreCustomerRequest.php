<?php

namespace App\Http\Requests\Crm;

use App\Support\Crm\CollaboratorScope;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
    }

    public function rules(): array
    {
        return [
            'affiliate_id' => ['nullable', 'exists:affiliates,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'google_maps_url' => ['nullable', 'url', 'max:500'],
            'prefecture' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'preferred_bicycle_line' => ['nullable', 'string', 'max:120'],
            'consultation_status' => ['nullable', 'in:new,consulting,confirmed,cancelled'],
            'purchase_interest' => ['nullable', 'string'],
            'consultation_history' => ['nullable', 'string'],
        ];
    }
}
