<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'user_id',
        'travel_request_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    /**
     * Relasi ke User (Siapa yang melakukan aksi)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke TravelRequest (Pengajuan mana yang diproses)
     */
    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }
}