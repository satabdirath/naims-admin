<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function fetchNotifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    public function clearNotifications()
    {
        Notification::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Notifications cleared']);
    }
}
