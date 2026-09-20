<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreLogisticsRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'transport_type'         => 'required|string|max:50',
            'booking_code_or_ticket' => 'required|string|max:100',
            'hotel_name'             => 'nullable|string|max:150',
            'departure_datetime'     => 'required|date',
            'return_datetime'        => 'required|date|after:departure_datetime',
        ];
    }
}