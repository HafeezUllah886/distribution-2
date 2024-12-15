<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\brands;
use App\Models\categories;
use App\Models\product_units;
use App\Models\products;
use App\Models\units;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($s_cat , $s_brand)
    {
        $products = products::query();

        if ($s_cat != 'all') {
            $products->where('catID', $s_cat);
        }

        if ($s_brand != 'all') {
            $products->where('brandID', $s_brand);
        }

        $items = $products->get();

        $cats = categories::orderBy('name', 'asc')->get();
        $brands = brands::orderBy('name', 'asc')->get();

        return view('products.product', compact('items', 'cats', 'brands', 's_cat', 's_brand'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cats = categories::orderBy('name', 'asc')->get();
        $brands = brands::orderBy('name', 'asc')->get();
        $units = units::all();
        $vendors = accounts::vendor()->get();

        return view('products.create', compact('cats', 'brands', 'units', 'vendors'));
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

        $product = products::create($request->only(['name', 'nameurdu', 'catID', 'brandID', 'pprice', 'price', 'discount', 'status', 'vendorID', 'fright', 'labor', 'claim']));

        $units = $request->unit_names;

        foreach($units as $key => $unit)
        {
            product_units::create(
                [
                    'productID' => $product->id,
                    'unit_name' => $unit,
                    'value' =>  $request->unit_values[$key],
                ]
            );
        }

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
    public function edit(products $product)
    {
        $cats = categories::orderBy('name', 'asc')->get();
        $brands = brands::orderBy('name', 'asc')->get();
        $units = units::all();
        $vendors = accounts::vendor()->get();

        return view('products.edit', compact('cats', 'brands', 'units', 'product', 'vendors'));
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
        $product->update($request->only(['name', 'nameurdu', 'catID', 'brandID', 'pprice', 'price', 'discount', 'status', 'vendorID', 'fright', 'labor', 'claim']));

        foreach($product->units as $unit)
        {
            $unit->delete();
        }

        $units = $request->unit_names;

        foreach($units as $key => $unit)
        {
            product_units::create(
                [
                    'productID' => $product->id,
                    'unit_name' => $unit,
                    'value' =>  $request->unit_values[$key],
                ]
            );
        }

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
