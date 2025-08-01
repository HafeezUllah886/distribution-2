<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\expense_categories;
use App\Models\expenses;
use Illuminate\Http\Request;

class ExpenseCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = expense_categories::currentBranch()->get();
        $branches = branches::all();

        return view('Finance.expense.category', compact('categories', 'branches'));
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

        expense_categories::create($request->all() + ['branchID' => auth()->user()->branchID]);

        return redirect()->route('expense_categories.index')->with('success', 'Expense Category Created Successfully');
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
        $cat = expense_categories::find($id);
        $cat->name = $request->name;
        $cat->branchID = $request->branchID;
        $cat->save();

        return redirect()->route('expense_categories.index')->with('success', 'Expense Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
