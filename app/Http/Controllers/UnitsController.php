<?php

namespace App\Http\Controllers;

use App\Models\branches;
use App\Models\units;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = units::orderBy('name', 'asc')->currentBranch()->get();
        $branches = branches::orderBy('name', 'asc')->get();

        return view('products.units', compact('units', 'branches'));
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
        units::create($request->all() + ['branchID' => auth()->user()->branchID]);
        return back()->with('success', 'Unit Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(units $units)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(units $units)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $unit = units::find($id);
        $unit->update($request->all());
        return back()->with('success', "Unit Updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(units $units)
    {
        //
    }

    public function getUnit($id)
    {
        $unit = units::find($id);

        return response()->json(
            [
                'unit_name' => $unit->name,
                'unit_value' => $unit->value,
                'unit_id' => $unit->id,
            ]
        );
    }
}
