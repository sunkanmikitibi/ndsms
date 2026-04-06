<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * Get extended validation rules for additional user fields including super admin fields.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>>
     */
    protected function extendedProfileRules(?int $userId = null): array
    {
        return [
            ...self::profileRules($userId),
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-\+\(\)\s]+$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'town' => ['nullable', 'string', 'max:100'],
            'organization' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get validation rules for super admin registration (all optional for super admin).
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>>
     */
    protected function superAdminRegistrationRules(?int $userId = null): array
    {
        return [
            ...self::extendedProfileRules($userId),
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9\-\+\(\)\s]+$/'],
            'address' => ['required', 'string', 'max:255'],
            'town' => ['required', 'string', 'max:100'],
            'organization' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
