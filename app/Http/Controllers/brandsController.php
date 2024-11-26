<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\brands;
use Illuminate\Http\Request;

class brandsController extends Controller
{
    public function index()
    {
        $brands = brands::orderBy('name', 'asc')->get();

        return view('products.brands', compact('brands'));
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
        brands::create($request->all());
        return back()->with('msg', 'Brand Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(brands $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(brands $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        brands::find($id)->update($request->all());
        return back()->with('msg', 'Brand Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(brands $brand)
    {
        //
    }
}
