<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTravelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorize dihandle Sanctum di route
    }

    public function rules(): array
    {
        return [
            'origin_city_id' => 'required|exists:ref_cities,id',
            'destination_city_id' => 'required|exists:ref_cities,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'Tanggal pulang tidak boleh mendahului tanggal keberangkatan.',
        ];
    }
}
