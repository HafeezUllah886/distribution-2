<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $categories = expense_categories::all();

        return view('Finance.expense.category', compact('categories'));
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
                'name' => 'required|unique:expense_categories,name'
            ]
        );

        expense_categories::create($request->all());

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
    public function update(Request $request, expense_categories $cat)
    {
        $request->validate(
            [
                'name' => 'required|unique:expense_categories,name,' . $cat->id
            ]
        );
        $cat->name = $request->name;
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
