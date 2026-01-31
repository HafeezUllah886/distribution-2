<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\location_tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'location' => $location,
        ], 200);

    }

    public function storeLocationCached(Request $request)
    {
        $data = $request->all();

        // Check if the request is a direct array of locations
        if (is_array($data) && (isset($data[0]) || empty($data))) {
            $data = ['locations' => $data];
        }

        $validator = Validator::make($data, [
            'locations' => 'required|array',
            'locations.*.latitude' => 'required',
            'locations.*.longitude' => 'required',
            'locations.*.date' => 'required',
            'locations.*.time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($data['locations'] as $item) {
                location_tracking::create([
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'date' => $item['date'],
                    'time' => $item['time'],
                    'userID' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => count($data['locations']).' Location(s) stored successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}
