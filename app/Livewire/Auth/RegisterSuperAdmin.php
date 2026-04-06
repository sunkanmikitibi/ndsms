<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateSuperAdminUser;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Livewire\Component;

class RegisterSuperAdmin extends Component
{
    use PasswordValidationRules, ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone = '';
    public string $address = '';
    public string $town = '';
    public string $organization = '';
    public string $position = '';
    public string $department = '';
    public string $state = '';
    public string $country = '';

    public function register(): void
    {
        $validated = $this->validate([
            ...$this->superAdminRegistrationRules(),
            'password' => $this->passwordRules(),
            'password_confirmation' => 'required|same:password',
        ]);

        $action = new CreateSuperAdminUser();
        $user = $action->create($validated);

        auth()->login($user);

        $this->redirect(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.register-super-admin');
    }
}
