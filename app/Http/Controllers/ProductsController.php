<?php

namespace App\Http\Controllers;

use App\Models\brands;
use App\Models\categories;
use App\Models\products;
use App\Models\units;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = products::all();
        $units = units::all();
        $cats = categories::orderBy('name', 'asc')->get();
        $brands = brands::orderBy('name', 'asc')->get();
        return view('products.product', compact('items', 'units', 'cats', 'brands'));
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
                'name' => "unique:products,name",
            ],
            [
            'name.unique' => "Product already Existing",
            ]
        );

        products::create($request->all());

        return back()->with('success', 'Product Created');
    }

    /**
     * Display the specified resource.
     */
    public function show($all)
    {
        $categories = categories::with('products')->get();
        return view('products.pricelist', compact('categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $products)
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
                'name' => "unique:products,name,".$id,
            ],
            [
            'name.unique' => "Product already Existing",
            ]
        );

        $product = products::find($id);
        $product->update($request->all());

        return back()->with('success', 'Product Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(products $products)
    {
        //
    }
}
