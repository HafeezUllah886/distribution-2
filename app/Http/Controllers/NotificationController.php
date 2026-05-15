<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::forUser(auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = Notification::forUser(auth()->id())->unread()->count();

        if ($request->ajax()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function getUnreadCount()
    {
        $count = Notification::forUser(auth()->id())->unread()->count();
        return response()->json(['count' => $count]);
    }

    public function getNotifications()
    {
        $notifications = Notification::forUser(auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::forUser(auth()->id())->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->find($id);
        if ($notification) {
            $notification->update(['status' => 'read']);
        }
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = Notification::where('user_id', auth()->id())->find($id);
        if ($notification) {
            $notification->delete();
        }
        return response()->json(['success' => true]);
    }

    public function clearAll()
    {
        Notification::where('user_id', auth()->id())->delete();
        return response()->json(['success' => true]);
    }
}