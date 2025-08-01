<?php

namespace App\Http\Controllers;

use App\Models\employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $desig = $request->designation ?? "All";
        $dept = $request->department ?? "All";   
        $employees = employee::currentBranch();
        if($desig != "All"){
            $employees = $employees->where('designation', $desig);
        }
        if($dept != "All"){
            $employees = $employees->where('department', $dept);
        }
        $employees = $employees->get();

        $designations = employee::currentBranch()->get()->unique('designation')->pluck('designation')->toArray();
        $departments = employee::currentBranch()->get()->unique('department')->pluck('department')->toArray();
        return view('employees.index', compact('employees', 'designations', 'departments', 'desig', 'dept'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employees = employee::create([
            'branchID' => auth()->user()->branchID,
            'name' => $request->name,
            'fname' => $request->fname,
            'designation' => $request->designation,
            'department' => $request->department,
            'contact' => $request->contact,
            'address' => $request->address,
            'salary' => $request->salary,
            'limit' => $request->limit,
            'doe' => $request->doe,
            'status' => $request->status,
        ]);
        return redirect()->route('employees.index')->with('success', 'Employee created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $employees = employee::find($id);
        $employees->update([
            'name' => $request->name,
            'fname' => $request->fname,
            'designation' => $request->designation,
            'department' => $request->department,
            'contact' => $request->contact,
            'address' => $request->address,
            'salary' => $request->salary,
            'limit' => $request->limit,
            'doe' => $request->doe,
            'status' => $request->status,
        ]);
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(employee $employees)
    {
        //
    }
}
