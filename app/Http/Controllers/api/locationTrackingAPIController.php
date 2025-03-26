<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\location_tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class locationTrackingAPIController extends Controller
{
    public function storeLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
                'errors' => $validator->errors()
            ], 422);
        }

        $location = location_tracking::create(
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'userID' => auth()->id(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Location stored successfully',
            'location' => $location
        ], 200);

    }

}
