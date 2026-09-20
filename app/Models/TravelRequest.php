<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelRequest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sppd_number',
        'requester_employee_id',
        'origin_city_id',
        'destination_city_id',
        'start_date',
        'end_date',
        'purpose',
        'estimated_total_cost',
        'advance_payment',
        'status',
        'cancel_reason_type',
        'cancel_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'estimated_total_cost' => 'decimal:2',
            'advance_payment' => 'decimal:2',
        ];
    }

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function originCity()
    {
        return $this->belongsTo(RefCity::class, 'origin_city_id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(RefCity::class, 'destination_city_id');
    }

    public function assignees()
    {
        return $this->hasMany(TravelAssignee::class);
    }

    public function logistics()
    {
        return $this->hasOne(TravelLogistics::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function expenseReport()
    {
        return $this->hasOne(ExpenseReport::class);
    }

    public function attachments()
    {
        return $this->hasMany(TravelAttachment::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'travel_request_id');
    }
}