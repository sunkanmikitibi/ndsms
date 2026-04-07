<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeRedirectController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        // Super admin → redirect to admin dashboard
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Everyone else (admin, approvals-officer, registry-officer, field-officer, citizen, etc.) → redirect to portal
        return redirect()->route('portal.dashboard');
    }
}
