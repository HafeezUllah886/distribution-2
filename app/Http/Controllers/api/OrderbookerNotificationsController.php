<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\orderbooker_notifications;
use Illuminate\Http\Request;

class OrderbookerNotificationsController extends Controller
{
    public function index(Request $request)
    {
        $notifications = orderbooker_notifications::where('orderbooker_id', $request->user()->id)->where('status', 'unread')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => 'Notifications retrieved successfully',
                'data' => [
                    'notifications' => $notifications,
                ],
            ],
        ], 200);
    }

    public function read(Request $request)
    {
        $notification = orderbooker_notifications::where('orderbooker_id', $request->user()->id)->find($request->id);
        $notification->status = 'read';
        $notification->save();

        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => 'Notification read successfully',
                'data' => [
                    'notification' => $notification,
                ],
            ],
        ], 200);
    }
}
