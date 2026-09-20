<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'expense_report_id',
        'file_type',
        'file_path',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function expenseReport()
    {
        return $this->belongsTo(ExpenseReport::class);
    }
}