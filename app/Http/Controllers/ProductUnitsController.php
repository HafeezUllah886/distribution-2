<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\product_units;
use App\Models\products;
use App\Models\units;
use Illuminate\Http\Request;

class ProductUnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
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

        $unit = units::find($request->unit);
        product_units::create(
            [
                'productID' => $request->product_id,
                'unit_name' => $unit->name,
                'value' => $unit->value,
            ]
        );

        return redirect()->back()->with('success', 'Unit Added');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product_units = product_units::where('productID', $id)->get();
        $product = products::find($id);

        $units = units::currentBranch()->get();

        return view('products.product_units', compact('product_units', 'product', 'units'));
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
        product_units::where('id', $id)->update(
            [
                'unit_name' => $request->unit_name,
                'value' => $request->value,
            ]
        );

        return redirect()->back()->with('success', 'Unit Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
