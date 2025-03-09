<?php

namespace App\Http\Controllers;

use App\Models\obsolete_stock;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ObsoleteStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $obsoletes = obsolete_stock::currentBranch()->orderBy('id', 'desc')->get();
        return view('stock.obsolete.index', compact('obsoletes'));
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
    public function show(obsolete_stock $obsolete_stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(obsolete_stock $obsolete_stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, obsolete_stock $obsolete_stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(obsolete_stock $obsolete_stock)
    {
        //
    }
}
