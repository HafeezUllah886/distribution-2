<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\area;
use App\Models\town;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = area::all();
        $towns = town::all();

        return view('area.index', compact('areas', 'towns'));
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
        $request->validate(
            [
                'name' => 'required|unique:areas,name'
            ]
        );

        area::create(
            [
                'townID' => $request->townID,
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
        $request->validate(
            [
                'name' => 'required|unique:areas,name,' . $area->id,
            ]
        );

        $area->update(
            [
                'townID' => $request->townID,
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
