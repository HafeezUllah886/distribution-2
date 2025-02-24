<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin_BranchAdmin_AccountantCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if (Auth::user()->role == 'Admin' || Auth::user()->role == "Branch Admin" || Auth::user()->role == "Accountant") {
            return $next($request);
        }
        else
        {
            abort(403, 'Unauthorized action.');
        }
    }
}
