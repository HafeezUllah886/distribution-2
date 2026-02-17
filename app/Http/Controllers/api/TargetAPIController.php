<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\targets;
use Illuminate\Support\Facades\DB;

class TargetAPIController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $targets = targets::where('orderbookerID', $user->id)
            ->with(['product.vendor', 'unit'])
            ->orderBy('endDate', 'desc')
            ->get();

        $data = $targets->map(function ($target) {
            $qtySold = DB::table('sale_details')
                ->where('orderbookerID', $target->orderbookerID)
                ->where('productID', $target->productID)
                ->whereBetween('date', [$target->startDate, $target->endDate])
                ->sum('pc');

            $targetQty = $target->pc / $target->unit_value;
            $achievedQty = $qtySold / $target->unit_value;
            $remainingQty = $targetQty - $achievedQty;

            // Cap for percentage calculation for status logic
            $qtySoldForPer = $qtySold > $target->pc ? $target->pc : $qtySold;
            $percentage = $target->pc > 0 ? round(($qtySoldForPer / $target->pc) * 100, 2) : 0;
            $actualPercentage = $target->pc > 0 ? round(($qtySold / $target->pc) * 100, 2) : 0;

            $isExpired = $target->endDate < now()->toDateString();
            $isAchieved = $percentage >= 100;

            return [
                'id' => $target->id,
                'product_name' => $target->product->name,
                'vendor_name' => $target->product->vendor->title ?? 'N/A',
                'unit_name' => $target->unit->unit_name,
                'pack_size' => $target->unit_value,
                'price' => round($target->product->price * $target->unit_value, 2),
                'target_qty' => round($targetQty, 2),
                'achieved_qty' => round($achievedQty, 2),
                'remaining_qty' => round($remainingQty, 2),
                'start_date' => $target->startDate,
                'end_date' => $target->endDate,
                'percentage' => $percentage,
                /* 'actual_percentage' => $actualPercentage, */
                'achievement_status' => $isAchieved ? 'ACHIEVED' : 'PENDING',
                'achievement_color' => $isAchieved ? 'success' : 'danger',
                'target_status' => $isExpired ? 'CLOSED' : 'ACTIVE',
                'target_status_color' => $isExpired ? 'danger' : 'success',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
