<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Mengambil list notifikasi untuk user yang sedang login
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    // Menandai SEMUA notifikasi menjadi "Sudah Dibaca"
    public function markAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai dibaca',
        ]);
    }
}
