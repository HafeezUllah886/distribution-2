<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderbookerBalanceController extends Controller
{
    public function balance(request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => getUserAccountBalance($request->user()->id),
            ]
        ], 200);
    }

    public function account_statement(request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }
        
        $user = $request->user();
        $from = $request->from;
        $to = $request->to;
        
        $transactions = users_transactions::where('userID', $user->id)->whereBetween('date', [$from, $to])->get();

        $pre_cr = users_transactions::where('userID', $user->id)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = users_transactions::where('userID', $user->id)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = users_transactions::where('userID', $user->id)->sum('cr');
        $cur_db = users_transactions::where('userID', $user->id)->sum('db');
        $cur_balance = $cur_cr - $cur_db;

        return response()->json([
            'status' => 'success',
            'data' => [
                'transactions' => $transactions,
                'pre_balance' => $pre_balance,
                'cur_balance' => $cur_balance,
                'from' => $from,
                'to' => $to
            ]
        ], 200);
    }

    public function method_wise_balance(Request $request)
    {

        $methods = ['Cash', 'Cheque', 'Online', 'Other'];

        foreach($methods as $method)
        {
            $balance = getMethodBalance($method, $request->user()->id);
            $data[] = [
                'method' => $method,
                'balance' => $balance,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }
}
