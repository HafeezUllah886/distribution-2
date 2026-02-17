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
        $notificationSettings = notification_settings::where('branch_id', auth()->user()->branch_id)->first();

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
        //
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
