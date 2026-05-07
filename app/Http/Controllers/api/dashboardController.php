<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\expenses;
use App\Models\products;
use App\Models\purchase_details;
use App\Models\sale_details;
use App\Models\sales;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function index()
    {
            if(auth()->user()->role == "Admin")
            {
                return to_route('admin.dashboard');
            }

            if(auth()->user()->role == "Operator")
            {
                return view('dashboard.operator_dashboard');
            }
            
            if(auth()->user()->role == "Branch Admin")
            {
                return view('dashboard.branchadmin_dashboard');
            }

            if(auth()->user()->role == "Accountant")
            {
                return view('dashboard.accountant_dashboard');
            }
    }
}
