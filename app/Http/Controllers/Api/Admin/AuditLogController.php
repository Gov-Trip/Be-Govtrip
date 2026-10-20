<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user.employee')->latest()->paginate(50);

        return response()->json(['success' => true, 'data' => $logs]);
    }
}
