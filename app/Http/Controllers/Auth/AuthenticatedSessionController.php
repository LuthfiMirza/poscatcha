<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ShiftController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function createBuyer(): View
    {
        return view('auth.buyer-login');
    }

    public function create_admin(): View
    {
        return view('auth.login_admin');
    }

    /**
     * Handle an incoming authentication request for cashier.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return $this->redirectAfterLogin($request, Auth::user());
    }

    public function storeBuyer(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->hasRole('buyer')) {
            return $this->redirectAfterLogin($request, $user);
        }

        return redirect()->intended(route('buyer.shop.index'));
    }

    protected function redirectAfterLogin(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard_admin');
        }

        if ($user->hasRole('cashier')) {
            if (! ShiftController::activeShiftForCashier($user->id)) {
                return redirect()->route('cashier.shift.open');
            }

            return redirect()->route('selling_product');
        }

        if ($user->hasRole('buyer')) {
            return redirect()->intended(route('buyer.shop.index'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Handle an incoming authentication request for admin.
     */
    public function store_admin(LoginRequest $request): RedirectResponse
    {
        return $this->authenticateAndRedirect($request, 'admin', 'dashboard_admin');
    }

    /**
     * Common authentication and redirect logic.
     */
    protected function authenticateAndRedirect(LoginRequest $request, string $requiredRole, string $redirectRoute): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->hasRole($requiredRole)) {
            if ($requiredRole === 'cashier' && ! ShiftController::activeShiftForCashier($user->id)) {
                return redirect()->route('cashier.shift.open');
            }

            return redirect()->route($redirectRoute);
        }

        // If user doesn't have the required role, log them out
        $this->performLogout($request);

        return redirect('/')->withErrors([
            'role' => 'You are not authorized to access this area.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->performLogout($request);

        return redirect()->route('buyer.shop.index')->with('success', 'Berhasil keluar dari akun.');
    }

    /**
     * Common logout logic.
     */
    protected function performLogout(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
