<?php
$expenses = \App\Models\expenses::whereBetween('date', ['2026-06-01', '2026-06-30'])->get()->groupBy('categoryID');
$expense_categories = \App\Models\expense_categories::all()->keyBy('id');
$expenses_data = [];
foreach ($expenses as $catID => $items) {
    $cat_name = isset($expense_categories[$catID]) ? $expense_categories[$catID]->name : 'Other';
    $sum = $items->sum('amount');
    $expenses_data[$cat_name] = ['sum' => $sum, 'details' => $items];
}
echo json_encode(array_keys($expenses_data));
