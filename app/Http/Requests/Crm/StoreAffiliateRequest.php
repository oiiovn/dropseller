<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class StoreAffiliateRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:affiliates,code'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:affiliates,phone'],
            'email' => ['nullable', 'email', 'max:255'],
            'area' => ['nullable', 'string', 'max:150'],
            'region' => ['nullable', 'in:japan,vietnam'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
            'joined_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
