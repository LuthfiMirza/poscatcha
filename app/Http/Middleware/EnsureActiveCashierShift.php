<?php

namespace App\Http\Middleware;

use App\Models\CashierShift;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveCashierShift
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('cashier')) {
            return $next($request);
        }

        $activeShift = CashierShift::query()
            ->open()
            ->where('cashier_id', $user->id)
            ->first();

        if (!$activeShift) {
            return redirect()
                ->route('cashier.shift.open')
                ->with('error', 'Anda harus membuka shift terlebih dahulu sebelum melakukan transaksi.');
        }

        return $next($request);
    }
}
