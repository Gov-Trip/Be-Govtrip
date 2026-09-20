<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'nip',
        'name',
        'position',
        'grade',
        'direct_supervisor_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function directSupervisor()
    {
        return $this->belongsTo(Employee::class, 'direct_supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'direct_supervisor_id');
    }

    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class, 'requester_employee_id');
    }

    public function assignments()
    {
        return $this->hasMany(TravelAssignee::class);
    }

    public function handledLogistics()
    {
        return $this->hasMany(TravelLogistics::class, 'admin_travel_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approver_employee_id');
    }

    public function expenseReports()
    {
        return $this->hasMany(ExpenseReport::class);
    }
}