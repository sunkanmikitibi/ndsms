<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeRedirectController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['super-admin', 'admin', 'approvals-officer', 'registry-officer'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('portal.dashboard');
    }
}
