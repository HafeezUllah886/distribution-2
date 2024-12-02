<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\area;
use App\Models\town;
use Illuminate\Http\Request;

class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
                'name' => 'required|unique:towns,name'
            ]
        );

        town::create(
            [
                'name' => $request->name
            ]
        );

        return back()->with('success', 'Town Created');
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
    public function update(Request $request, town $town)
    {
        $request->validate(
            [
                'name' => 'required|unique:towns,name,' . $town->id,
            ]
        );

        $town->update(
            [
                'name' => $request->name,
            ]
        );

        return back()->with('success', "Town Updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
