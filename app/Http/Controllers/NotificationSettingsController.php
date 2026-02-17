<?php

namespace App\Http\Controllers;

use App\Models\notification_settings;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificationSettings = notification_settings::where('branch_id', auth()->user()->branchID)->first();

        return view('notification_settings.index', compact('notificationSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        notification_settings::updateOrCreate([
            'branch_id' => auth()->user()->branchID,
        ], [
            'branch_id' => auth()->user()->branchID,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'intervals' => $request->intervals,
            'week_days' => $request->week_days,
        ]);

        return redirect()->route('notification_settings.index')->with('success', 'Notification settings updated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(notification_settings $notification_settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(notification_settings $notification_settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, notification_settings $notification_settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(notification_settings $notification_settings)
    {
        //
    }
}
