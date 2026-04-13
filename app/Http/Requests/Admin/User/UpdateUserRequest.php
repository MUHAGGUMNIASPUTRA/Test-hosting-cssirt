<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'current_password' => ['required', 'string'],
            'password'         => ['nullable', 'confirmed', Rules\Password::min(8)],
            'role'             => ['required', Rule::enum(UserRole::class)],
        ];
    }
}
