<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->extendedProfileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'phone' => $input['phone'] ?? null,
            'town' => $input['town'] ?? null,
            'address' => $input['address'] ?? null,
            'organization' => $input['organization'] ?? null,
            'position' => $input['position'] ?? null,
            'department' => $input['department'] ?? null,
            'state' => $input['state'] ?? null,
            'country' => $input['country'] ?? null,
        ]);

        // Automatically assign field-officer role to newly registered users
        $user->assignRole('field-officer');

        // Dispatch Welcome Email and Dashboard Notification
        $user->notify(new \App\Notifications\WelcomeToNdsmsNotification());

        return $user;
    }
}
