<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\notification_settings;

class NotificationSettingsAPIController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $notificationSettings = notification_settings::where('branch_id', $user->branchID)->first();

        return [
            'start_time' => $notificationSettings->start_time,
            'end_time' => $notificationSettings->end_time,
            'intervals' => $notificationSettings->intervals,
            'week_days' => $notificationSettings->week_days,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
