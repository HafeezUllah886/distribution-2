<?php

namespace App\Http\Controllers;

use App\Models\employees_payment_cats;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeesPaymentCatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = employees_payment_cats::all();

        return view('employees.issue_misc.cats.category', compact('categories'));
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
                'name' => 'required|unique:employees_payment_cats,name'
            ]
        );

        employees_payment_cats::create($request->all());

        return redirect()->route('issue_misc_cats.index')->with('success', 'Employee Payment Category Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(employees_payment_cats $employees_payment_cats)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(employees_payment_cats $employees_payment_cats)
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
                'name' => 'required|unique:employees_payment_cats,name,' . $id
            ]
        );
        employees_payment_cats::find($id)->update([
            'name' => $request->name
        ]);

        return redirect()->route('issue_misc_cats.index')->with('success', 'Employee Payment Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(employees_payment_cats $employees_payment_cats)
    {
        //
    }
}
