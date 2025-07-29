<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\area;
use App\Models\product_dc;
use App\Models\products;
use Illuminate\Http\Request;

class productDCController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $areas = area::currentBranch()->get();
        $product = products::findOrFail($id);
        foreach ($areas as $area)
        {   
            $dc = product_dc::where('productID', $id)->where('areaID', $area->id)->first();
            $area->dc = $dc->dc ?? 0;
        }

        return view('products.dcs', compact('areas', 'product'));
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
        $areas = $request->areaID;
        foreach ($areas as $key => $area)
        {
            product_dc::updateOrCreate(['productID' => $id, 'areaID' => $request->areaID[$key]], ['dc' => $request->value[$key] ?? 0]);
        }

        return back()->with('success', 'Delivery Charges Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
