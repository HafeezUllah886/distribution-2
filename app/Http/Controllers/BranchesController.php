<?php

namespace App\Http\Controllers;

use App\Models\branches;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = branches::all();
        return view('branches.index', compact('branches'));
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
        branches::create($request->all());
        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(branches $branches)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(branches $branche)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, branches $branch)
    {
        $branch->update($request->all());
        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(branches $branches)
    {
        //
    }
}
