<?php

use App\Models\attachment;
use App\Models\currency_transactions;
use App\Models\method_transactions;
use App\Models\ref;
use App\Models\transactions;
use App\Models\userAccounts;
use App\Models\users_transactions;

function createTransaction($accountID, $date, $cr, $db, $notes, $ref){
    transactions::create(
        [
            'accountID' => $accountID,
            'date' => $date,
            'cr' => $cr,
            'db' => $db,
            'notes' => $notes,
            'refID' => $ref,
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

function createMethodTransaction($user, $method, $cr, $db, $date, $number, $bank, $remarks, $notes, $ref){
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
            'remarks' => $remarks,
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
    $transactions  = transactions::where('accountID', $id);

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
