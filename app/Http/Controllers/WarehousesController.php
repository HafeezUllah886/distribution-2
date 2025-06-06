<?php

namespace App\Http\Controllers;

use App\Models\branches;
use App\Models\warehouses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehousesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role == 'Admin') {
            $warehouses = warehouses::all();
            $branches = branches::all();
        } else {
            $warehouses = warehouses::currentBranch()->get();
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }
       

        return view('warehouses.index', compact('warehouses', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        warehouses::create($request->all());

        return back()->with("success", "Warehouse Created");
    }

    /**
     * Display the specified resource.
     */
    public function show(warehouses $warehouses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(warehouses $warehouses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, warehouses $warehouse)
    {
        $warehouse->update($request->all());

        return back()->with('success', "Warehouse Updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(warehouses $warehouses)
    {
        //
    }
}
