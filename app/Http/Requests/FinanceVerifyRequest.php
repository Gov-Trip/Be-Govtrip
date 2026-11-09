<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_valid'         => 'required|boolean',
            'advance_payment'  => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
            'rejection_reason' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'is_valid.required' => 'Keputusan verifikasi (valid / tidak valid) wajib dipilih.',
        ];
    }
}