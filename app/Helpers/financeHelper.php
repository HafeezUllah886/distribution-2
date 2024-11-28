<?php

use App\Models\attachment;
use App\Models\currency_transactions;
use App\Models\ref;
use App\Models\transactions;
use App\Models\userAccounts;

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

function createCurrencyTransaction($accountID, $currencyID, $currency, $type ,$date, $notes, $ref){
    foreach($currencyID as $key => $id)
    {
        if($type == "cr")
        {
            currency_transactions::create(
                [
                    'accountID' => $accountID,
                    'currencyID' => $id,
                    'date' => $date,
                    'cr' => $currency[$key],
                    'notes' => $notes,
                    'refID' => $ref,
                ]
            );
        }
        else
        {
            currency_transactions::create(
                [
                    'accountID' => $accountID,
                    'currencyID' => $id,
                    'date' => $date,
                    'db' => $currency[$key],
                    'notes' => $notes,
                    'refID' => $ref,
                ]
            );
        }
    }
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
    $userAccount = userAccounts::where('userID', $id)->first();
    $transactions  = transactions::where('accountID', $userAccount->accountID);

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
