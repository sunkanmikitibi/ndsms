<?php

namespace App\Http\Requests;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class SuperAdminRegistrationRequest extends FormRequest
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust if you want to restrict super admin registration
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            ...$this->superAdminRegistrationRules(),
            'password' => $this->passwordRules(),
            'password_confirmation' => 'required|same:password',
        ];
    }
}
