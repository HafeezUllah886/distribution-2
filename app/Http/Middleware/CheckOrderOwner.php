<?php

namespace App\Http\Middleware;

use App\Models\orders;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOrderOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $order = orders::find($request->id); // Get the sale by ID from the request

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Check if the authenticated user is the owner of the sale
        if ($order->status == "Completed") {
            // If the user is not the owner, return a 403 forbidden response
            return response()->json(['message' => 'Order Already Completed'], 403);
        }
        if (auth()->user()->role !== "Admin" ) {
            return response()->json(['message' => 'You are not allowed to delete this order'], 403);
        }

        // If the user is the owner, allow the request to proceed
        return $next($request);
    }
}
