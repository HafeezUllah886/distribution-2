<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\orders;
use Illuminate\Http\Request;

class BranchOrdersController extends Controller
{
    public function index(request $request)
    {
        $from = $request->start ?? now()->toDateString();
        $to = $request->end ?? now()->toDateString();
       
        $orders = orders::with('customer.area', 'details.product', 'details.unit')->currentBranch()->whereBetween("date", [$from, $to])->orderBy('id', 'desc')->get();

        return view('orders.index', compact('orders', 'from', 'to'));
    }
}
