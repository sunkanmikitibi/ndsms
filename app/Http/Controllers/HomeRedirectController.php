<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeRedirectController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        // Admin roles → redirect to admin dashboard
        if ($user->hasAnyRole(['super-admin', 'admin', 'approvals-officer', 'registry-officer'])) {
            return redirect()->route('admin.dashboard');
        }

        // Everyone else (field-officer, citizen, etc.) → redirect to portal
        return redirect()->route('portal.dashboard');
    }
}
