<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\expense_categories;
use App\Models\expenses;
use Illuminate\Http\Request;

class ExpenseReportController extends Controller
{
    public function index()
    {
        $cats = expense_categories::currentBranch()->get();

        return view('reports.expense.index', compact('cats'));
    }


    public function details(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? lastDayOfMonth();
        $cat = $request->cat ?? "All";

        $expense = expenses::whereBetween('date', [$from, $to])->currentBranch();

        $category = "All";

        if($cat != "All")
        {
            $expense = $expense->where('categoryID', $cat);
            $category = expense_categories::find($cat)->name;
        }

        $expenses = $expense->with('category')->get();
        $groupedExpenses = $expenses->groupBy('category.name');
        
        // Convert to array and sort
        $data = [];
        foreach ($groupedExpenses as $categoryName => $categoryExpenses) {
            $data[$categoryName] = $categoryExpenses->toArray();
        }
        ksort($data);

        return view('reports.expense.details', compact('from', 'to', 'data', 'category', 'expenses'));

    }
}
