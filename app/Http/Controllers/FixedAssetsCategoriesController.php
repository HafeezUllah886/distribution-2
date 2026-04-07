<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\fixed_assets_categories;
use Illuminate\Http\Request;

class FixedAssetsCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = fixed_assets_categories::currentBranch()->get();
        $branches = branches::all();

        return view('Finance.fixed_assets.category', compact('categories', 'branches'));
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
                'name' => 'required'
            ]
        );

        fixed_assets_categories::create($request->all() + ['branchID' => auth()->user()->branchID]);

        return redirect()->route('fixed_asset_categories.index')->with('success', 'Fixed Asset Category Created Successfully');
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
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'required'
            ]
        );
        $cat = fixed_assets_categories::find($id);
        $cat->name = $request->name;
        $cat->branchID = $request->branchID;
        $cat->save();

        return redirect()->route('fixed_asset_categories.index')->with('success', 'Fixed Asset Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
