<?php

use App\Models\attachment;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\employee_ledger;
use App\Models\method_transactions;
use App\Models\ref;
use App\Models\transactions;
use App\Models\userAccounts;
use App\Models\users_transactions;

function createTransaction($accountID, $date, $cr, $db, $notes, $ref, $orderbookerID){
    transactions::create(
        [
            'accountID' => $accountID,
            'date' => $date,
            'cr' => $cr,
            'db' => $db,
            'notes' => $notes,
            'refID' => $ref,
            'orderbookerID' => $orderbookerID == 0 ? null : $orderbookerID,
        ]
    );

}

function createUserTransaction($userID, $date, $cr, $db, $notes, $ref){
    users_transactions::create(
        [
            'userID' => $userID,
            'date' => $date,
            'cr' => $cr,
            'db' => $db,
            'notes' => $notes,
            'refID' => $ref,
        ]
    );
}
function createEmployeeTransaction($employeeID, $date, $cr, $db, $notes, $ref){
    employee_ledger::create(
        [
            'employeeID' => $employeeID,
            'date' => $date,
            'cr' => $cr,
            'db' => $db,
            'notes' => $notes,
            'refID' => $ref,
        ]
    );
}

function createCurrencyTransaction($userID, $currencyID, $qty, $type ,$date, $notes, $ref){
    foreach($currencyID as $key => $id)
    {
        if($qty[$key] > 0)
        {
            if($type == "cr")
            {
                currency_transactions::create(
                    [
                        'userID' => $userID,
                        'currencyID' => $id,
                        'date' => $date,
                        'cr' => $qty[$key],
                        'notes' => $notes,
                        'refID' => $ref,
                    ]
                );
            }
            else
            {
                currency_transactions::create(
                    [
                        'userID' => $userID,
                        'currencyID' => $id,
                        'date' => $date,
                        'db' => $qty[$key],
                        'notes' => $notes,
                        'refID' => $ref,
                    ]
                );
            }
        }

    }
}

function createMethodTransaction($user, $method, $cr, $db, $date, $number, $bank, $cheque_date, $notes, $ref){
    method_transactions::create(
        [
            'userID' => $user,
            'branchID' => auth()->user()->branchID,
            'method' => $method,
            'date' => $date,
            'cr' => $cr,
            'db' => $db,
            'number' => $number,
            'bank' => $bank,
            'cheque_date' => $cheque_date ?? now(),
            'notes' => $notes,
            'refID' => $ref,
        ]
    );
}

function deleteAttachment($ref)
{
    $attachment = attachment::where('refID', $ref)->first();
    if (file_exists(public_path($attachment->path))) {
        unlink(public_path($attachment->path));
    }
    $attachment->delete();
}

function createAttachment($file, $ref)
{
    $filename = time() . '.' . $file->getClientOriginalExtension();
    $file->move('attachments', $filename);

    attachment::create(
        [
            'path' => "attachments/" . $filename,
            'refID' => $ref,
        ]
    );
}

function getAccountBalance($id){
    $cr = transactions::where('accountID', $id)
            ->selectRaw('CAST(SUM(cr) AS DECIMAL(15,2)) as total')->value('total');
    $db = transactions::where('accountID', $id)
            ->selectRaw('CAST(SUM(db) AS DECIMAL(15,2)) as total')->value('total');
    return $cr - $db;
}
function getEmployeeBalance($id){
    $transactions  = employee_ledger::where('employeeID', $id);

    $cr = $transactions->sum('cr');
    $db = $transactions->sum('db');
    $balance = $cr - $db;

    return $balance;
}

function getAccountBalanceOrderbookerWise($accountID, $orderbookerID){
    $transactions  = transactions::where('accountID', $accountID)->where('orderbookerID', $orderbookerID);

    $cr = $transactions->sum('cr');
    $db = $transactions->sum('db');
    $balance = $cr - $db;

    return $balance;
}

function getUserAccountBalance($id){
    $transactions  = users_transactions::where('userID', $id);

    $cr = $transactions->sum('cr');
    $db = $transactions->sum('db');
    $balance = $cr - $db;

    return $balance;
}


function getCurrencyBalance($id, $user){
    $transactions  = currency_transactions::where('currencyID', $id)->where('userID', $user);

    $cr = $transactions->sum('cr');
    $db = $transactions->sum('db');
    $balance = $cr - $db;

    return $balance;
}
function getMethodBalance($method, $user){
    $transactions  = method_transactions::where('method', $method)->where('userID', $user);

    $cr = $transactions->sum('cr');
    $db = $transactions->sum('db');
    $balance = $cr - $db;

    return $balance;
}


function checkMethodExceed($method, $user, $amount){
    $balance = getMethodBalance($method, $user);
    if($balance < $amount)
    {
        return false;
    }
    return true;
}

function checkCurrencyExceed($userID, $currencyID, $qty){
    foreach($currencyID as $key => $id)
    {
        if($qty[$key] > 0)
        {
            $balance = getCurrencyBalance($id, $userID);
            if($balance < $qty[$key])
            {
                return false;
            }
        }
    }
    return true;
}

function checkUserAccountExceed($userID, $amount){
    $balance = getUserAccountBalance($userID);
    if($balance < $amount)
    {
        return false;
    }
    return true;
}


function numberToWords($number)
{
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return ucfirst($f->format($number));
}


function spotBalanceBefore($id, $ref)
{
    $cr = transactions::where('accountID', $id)->where('refID', '<', $ref)->sum('cr');
    $db = transactions::where('accountID', $id)->where('refID', '<', $ref)->sum('db');
    return $balance = $cr - $db;
}

function spotBalance($id, $ref)
{
    $cr = transactions::where('accountID', $id)->where('refID', '<=', $ref)->sum('cr');
    $db = transactions::where('accountID', $id)->where('refID', '<=', $ref)->sum('db');
    return $balance = $cr - $db;
}

function accountTillDateBalance($id, $date)
{
    $cr = transactions::where('accountID', $id)->whereDate('date', '<=', $date)->sum('cr');
    $db = transactions::where('accountID', $id)->whereDate('date', '<=', $date)->sum('db');
    return $balance = $cr - $db;
}

function spotUserBalanceBefore($id, $ref)
{
    $cr = users_transactions::where('userID', $id)->where('refID', '<', $ref)->sum('cr');
    $db = users_transactions::where('userID', $id)->where('refID', '<', $ref)->sum('db');
    return $balance = $cr - $db;
}

function spotUserBalance($id, $ref)
{
    $cr = users_transactions::where('userID', $id)->where('refID', '<=', $ref)->sum('cr');
    $db = users_transactions::where('userID', $id)->where('refID', '<=', $ref)->sum('db');
    return $balance = $cr - $db;
}


function saveCheque($customerID, $userID, $orderbookerID, $chequeDate, $amount, $number, $bank, $notes, $ref){
    cheques::create(
        [
            'customerID' => $customerID,
            'userID' => $userID,
            'orderbookerID' => $orderbookerID,
            'branchID' => auth()->user()->branchID,
            'cheque_date' => $chequeDate,
            'amount' => $amount,
            'number' => $number,
            'bank' => $bank,
            'notes' => $notes,
            'refID' => $ref,
        ]
    );
}