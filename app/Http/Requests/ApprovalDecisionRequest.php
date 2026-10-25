<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Sesuai 3 cabang keputusan HRD
            'decision' => 'required|in:Disetujui,Ditolak,Perlu Revisi,Direvisi',
            'notes' => 'nullable|string|max:500',
            'rejection_reason' => 'nullable|string|mas:500',
        ];
    }
}
