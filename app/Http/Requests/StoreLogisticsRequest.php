<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogisticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transport_type'         => 'required|string|in:Pesawat Udara,Kereta Api,Bus / Travel,Kendaraan Dinas,Kapal Laut',
            'booking_code_or_ticket' => 'required|string|max:100',
            'hotel_name'             => 'nullable|string|max:150',
            'departure_datetime'     => 'required|date',
            'return_datetime'        => 'required|date|after:departure_datetime',
            'estimated_total_cost'   => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'transport_type.in'     => 'Jenis transportasi harus sesuai dengan standar master transportasi.',
            'return_datetime.after' => 'Waktu kepulangan harus setelah waktu keberangkatan.',
        ];
    }
}