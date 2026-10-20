<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_real_expense' => 'required|numeric|min:0',
            'activity_summary' => 'required|string|min:10',
        ];
    }
}
