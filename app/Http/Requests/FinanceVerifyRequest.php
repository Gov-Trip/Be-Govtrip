<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Jangan lupa ini true!
    }

    public function rules(): array
    {
        return [
            'is_valid' => 'required|boolean', // Wajib boolean (true/false)
            'advance_payment' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'finance_notes.required_if' => 'Catatan revisi wajib diisi jika laporan SPJ tidak valid atau perlu diperbaiki.'
        ];
    }
}