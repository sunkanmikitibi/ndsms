<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateSuperAdminUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered super admin user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->superAdminRegistrationRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'phone' => $input['phone'],
            'address' => $input['address'] ?? null,
            'town' => $input['town'],
            'organization' => $input['organization'],
            'position' => $input['position'],
            'department' => $input['department'] ?? null,
            'state' => $input['state'] ?? null,
            'country' => $input['country'] ?? null,
            'is_super_admin' => true,
        ]);

        // Automatically assign super-admin role
        $user->assignRole('super-admin');

        return $user;
    }
}
