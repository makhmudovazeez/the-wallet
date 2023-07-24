<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['nullable', Rule::requiredIf(function () {
                return !$this->request->has('username');
            })],
            'username' => ['nullable', Rule::requiredIf(function() {
                return !$this->request->has('email');
            })],
            'password' => ['required']
        ];
    }
}
