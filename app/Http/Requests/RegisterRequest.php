<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', Rule::in(['private', 'pro'])],
            'address' => ['required', 'string', 'max:255'],
            'company_name' => ['required_if:account_type,pro', 'string', 'max:255'],
            'cfe_number' => ['required_if:account_type,pro', 'string', 'max:255'],
            'company_address' => ['required_if:account_type,pro', 'string', 'max:255'],
        ];
    }
}
