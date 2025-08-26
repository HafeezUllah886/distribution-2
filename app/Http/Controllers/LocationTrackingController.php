<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\location_tracking;
use App\Models\User;
use Illuminate\Http\Request;

class LocationTrackingController extends Controller
{
    public function index()
    {
        $users = User::orderbookers()->currentBranch()->get();

        return view('location_tracking.index', compact('users'));
    }

    public function tracking(Request $request)
    {
        $user = User::find($request->userID);
        $date = $request->date;
        

        $times = location_tracking::where('userID', $user->id)
        ->whereDate('date', $date)
        ->orderBy('time', 'asc')
        ->distinct()
        ->pluck('time')
        ->toArray();


        return view('location_tracking.map', compact('user',  'date', 'times'));
    }

    public function getLocations(Request $request)
    {
       

        $locations = location_tracking::where('userID', $request->user_id)
            ->whereDate('date', $request->date)
            ->whereBetween('time', [$request->start, $request->end])
            ->orderBy('time', 'asc')
            ->distinct()
            ->get(['latitude', 'longitude', 'time']);

        return response()->json($locations);
    }
}
