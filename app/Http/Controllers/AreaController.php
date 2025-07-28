<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\area;
use App\Models\branches;
use App\Models\town;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        
        if(Auth()->user()->role == "Admin")
        {
            $branches = branches::all();
            $areas = area::all();
            $towns = town::all();
        }
        else
        {
            $branches = branches::where('id', Auth()->user()->branchID)->get();
            $areas = area::where('branchID', Auth()->user()->branchID)->get();
            $towns = town::where('branchID', Auth()->user()->branchID)->get();
        }

        return view('area.index', compact('areas', 'towns', 'branches'));
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

        area::create(
            [
                'townID' => $request->townID,
                'branchID' => $request->branchID,
                'name' => $request->name
            ]
        );

        return back()->with('success', 'Area Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, area $area)
    {
        $area->update(
            [
                'townID' => $request->townID,
                'branchID' => $request->branchID,
                'name' => $request->name,
            ]
        );

        return back()->with('success', "Area Updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
