<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\balance_targets;
use App\Models\transactions;

class BalanceTargetAPIController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $targets = balance_targets::where('orderbookerID', $user->id)
            ->with(['customer', 'branch'])
            ->orderBy('endDate', 'desc')
            ->get();

        $data = $targets->map(function ($target) {
            $credits = transactions::where('accountID', $target->customerID)
                ->where('orderbookerID', $target->orderbookerID)
                ->whereDate('date', '<=', $target->endDate)
                ->sum('cr') ?? 0;
            $debits = transactions::where('accountID', $target->customerID)
                ->where('orderbookerID', $target->orderbookerID)
                ->whereDate('date', '<=', $target->endDate)
                ->sum('db') ?? 0;

            $current_balance = $credits - $debits;

            $total_reduction_needed = $target->start_value - $target->target_value;
            $current_reduction = $target->start_value - $current_balance;

            if ($total_reduction_needed > 0) {
                $per = ($current_reduction / $total_reduction_needed) * 100;
            } else {
                $per = $current_balance <= $target->target_value ? 100 : 0;
            }

            $display_per = $per < 0 ? 0 : ($per > 100 ? 100 : $per);
            $totalPer = round($display_per, 2);

            $isExpired = $target->endDate < now()->toDateString();
            $isAchieved = $totalPer >= 100;

            $achievement_status = '';
            $achievement_color = '';
            if ($isAchieved) {
                $achievement_status = 'Achieved';
                $achievement_color = 'success';
            } elseif (! $isExpired) {
                $achievement_status = 'In Progress';
                $achievement_color = 'info';
            } else {
                $achievement_status = 'Not Achieved';
                $achievement_color = 'danger';
            }

            return [
                'id' => $target->id,
                'customer_name' => $target->customer->title ?? 'N/A',
                'branch_name' => $target->branch->name ?? 'N/A',
                'start_value' => round($target->start_value, 2),
                'target_value' => round($target->target_value, 2),
                'current_balance' => round($current_balance, 2),
                'total_reduction_needed' => round($total_reduction_needed, 2),
                'current_reduction' => round($current_reduction, 2),
                'percentage' => $totalPer,
                'start_date' => $target->startDate,
                'end_date' => $target->endDate,
                'achievement_status' => $achievement_status,
                'achievement_color' => $achievement_color,
                'target_status' => $isExpired ? 'Closed' : 'Open',
                'target_status_color' => $isExpired ? 'warning' : 'success',
                'notes' => $target->notes,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
