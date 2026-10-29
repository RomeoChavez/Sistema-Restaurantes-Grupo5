<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:60'],
            'apellido' => ['required', 'string', 'max:60'],
            'telefono' => ['nullable', 'string', 'regex:/^[0-9+ ]{7,20}$/', 'unique:clientes,telefono'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function attributes(): array
    {
        return [
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }

    public function messages(): array
    {
        return [
            'telefono.regex' => 'El teléfono solo admite números, espacios y el signo +, entre 7 y 20 caracteres.',
            'telefono.unique' => 'Ya existe un cliente registrado con ese teléfono.',
            'email.unique' => 'Ya existe una cuenta con ese correo electrónico.',
        ];
    }
}