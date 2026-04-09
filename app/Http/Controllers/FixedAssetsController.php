<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\expense_categories;
use App\Models\expenses;
use App\Models\fixed_assets;
use App\Models\fixed_assets_categories;
use App\Models\fixed_assets_sales;
use App\Models\method_transactions;
use App\Models\staffPayments;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isFinite;

class FixedAssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->start ?? null;
        $to = $request->end ?? null;
        $categoryID = $request->category ?? "All";
        $status = $request->status ?? "All";

        $fixed_assets = fixed_assets::currentBranch()->orderby('id', 'desc');
        if($categoryID !== "All")
        {
            $fixed_assets = $fixed_assets->where('categoryID', $categoryID);
        }
        if($from !== null && $to !== null)
        {
            $fixed_assets = $fixed_assets->whereBetween('date', [$from, $to]);
        }
        if($status !== "All")
        {
            if($status === "Sold")
            {
                $fixed_assets = $fixed_assets->whereHas('sale');
            }
            else
            {
                $fixed_assets = $fixed_assets->whereDoesntHave('sale');
            }
        }
        $fixed_assets = $fixed_assets->get();

        $currencies = currencymgmt::all();
        foreach($currencies as $currency)
        {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        $categories = fixed_assets_categories::all();
        return view('Finance.fixed_assets.index', compact('fixed_assets', 'currencies', 'categories', 'from', 'to', 'categoryID', 'status'));
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
        try
        {
            DB::beginTransaction();
            if(!checkMethodExceed($request->method, auth()->user()->id, $request->amount))
            {
             throw new \Exception("Method Amount Exceed");
            }
            if(!checkUserAccountExceed(auth()->user()->id, $request->amount))
            {
             throw new \Exception("User Account Amount Exceed");
            }
           if($request->method == 'Cash')
           {
             if(!checkCurrencyExceed(auth()->user()->id, $request->currencyID, $request->qty))
             {
                 throw new \Exception("Currency Qty Exceed");
             }
           }
            $ref = getRef();
            fixed_assets::create(
                [
                    'item_description' => $request->item_description,
                    'amount' => $request->amount,
                    'branchID' => auth()->user()->branchID,
                    'categoryID' => $request->category,
                    'date' => $request->date,
                    'purchase_status' => $request->purchase_status,
                    'method' => $request->method,
                    'number' => $request->number,
                    'bank' => $request->bank,
                    'cheque_date' => $request->cheque_date,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );
            if($request->purchase_status == 'new')
            {
                $category_name = fixed_assets_categories::find($request->category)->name;
                $notes = "Fixed Asset Purchase Category: $category_name Item: $request->item_description - Method ".$request->method." Notes : ".$request->notes;
            createMethodTransaction(auth()->user()->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);
           
            createUserTransaction(auth()->user()->id, $request->date,0, $request->amount, $notes, $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, $notes, $ref);
            }
            }
            
            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }

            DB::commit();
            return redirect()->route('fixed_assets.index')->with('success', 'Fixed Asset Purchase Saved');
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
     public function show($id)
    {
        $asset = fixed_assets::find($id);
        $currencies = currencymgmt::all();

        if($asset->method == "Cash")
        {
          
            foreach($currencies as $currency)
            {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $asset->refID)->first();

                $currency->qty = $currenyTransaction->db ?? 0;
            }

        }

        $sale_currencies = currencymgmt::all();

        if($asset->status() == "Sold")
        {
            if($asset->sale->method == "Cash")
            {
                foreach($sale_currencies as $currency)
                {
                    $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $asset->sale->refID)->first();

                    $currency->qty = $currenyTransaction->cr ?? 0;
                }
            }
        }

        return view('Finance.fixed_assets.receipt', compact('asset', 'currencies', 'sale_currencies'));
    }

    public function saleCreate($id)
    {
        $asset = fixed_assets::find($id);
        $currencies = currencymgmt::all();
        foreach($currencies as $currency)
        {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        return view('Finance.fixed_assets.sale', compact('asset', 'currencies'));
    }

    public function sale(Request $request)
    {
         try{ 
            DB::beginTransaction();
            $ref = getRef();
            fixed_assets_sales::create(
                [
                    'fixedAssetID'  => $request->id,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'method'        => $request->method,
                    'number'        => $request->number,
                    'bank'          => $request->bank,
                    'cheque_date'   => $request->cheque_date,
                    'notes'         => $request->notes,
                    'refID'         => $ref,
                ]
            );
            $user_name = auth()->user()->name;
            if($request->method == 'Cheque')
            {

                throw new \Exception("Cheque Receiving Not Allowed");
            }
            else
            {
                $asset = fixed_assets::find($request->id);
                $category = fixed_assets_categories::find($asset->categoryID)->name;
                $notes = "Fixed Asset Sold Category: $category Item: $asset->item_description - Method $request->method Notes : $request->notes";
                $notes1 = "Fixed Asset Sold Category: $category Item: $asset->item_description - Method $request->method Notes : $request->notes";
            }
            
            createMethodTransaction(auth()->user()->id,$request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);
    
            createUserTransaction(auth()->user()->id, $request->date, $request->amount, 0, $notes, $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'cr', $request->date, $notes, $ref);
            }

            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }

          DB::commit();
            return back()->with('success', "Payment Saved");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        } 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(expenses $expenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, expenses $expenses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($ref)
    {
        try
        {
            DB::beginTransaction();
            $fixed = fixed_assets::where('refID', $ref)->first();
            if($fixed->status() == 'Sold')
            {
                $sale = fixed_assets_sales::where('fixedAssetID', $fixed->id)->first();
               
                users_transactions::where('refID', $sale->refID)->delete();
                currency_transactions::where('refID', $sale->refID)->delete();
                method_transactions::where('refID', $sale->refID)->delete();
                cheques::where('refID', $sale->refID)->delete();
                staffPayments::where('refID', $sale->refID)->delete();
                 $sale->delete();
            }
            $fixed->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            staffPayments::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('fixed_assets.index')->with('success', "Expense Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('fixed_assets.index')->with('error', $e->getMessage());
        }
    }
}
