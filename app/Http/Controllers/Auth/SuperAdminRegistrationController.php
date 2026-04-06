<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateSuperAdminUser;
use App\Http\Requests\SuperAdminRegistrationRequest;
use Illuminate\Routing\Controller;

class SuperAdminRegistrationController extends Controller
{
    /**
     * Show the super admin registration form.
     */
    public function show()
    {
        return view('livewire.auth.register-super-admin');
    }

    /**
     * Store a newly registered super admin user.
     */
    public function store(SuperAdminRegistrationRequest $request)
    {
        $action = new CreateSuperAdminUser();
        $user = $action->create($request->validated());

        auth()->login($user);

        return redirect(route('dashboard'));
    }
}
