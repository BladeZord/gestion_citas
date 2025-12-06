<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:usuarios,id',
            'password_actual' => 'required|string',
            'password_nueva' => 'required|string|min:8|confirmed',
            'password_nueva_confirmation' => 'required|string|min:8',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'El ID del usuario es requerido',
            'id.integer' => 'El ID debe ser un número entero',
            'id.exists' => 'El usuario no existe',
            'password_actual.required' => 'La contraseña actual es requerida',
            'password_nueva.required' => 'La nueva contraseña es requerida',
            'password_nueva.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'password_nueva.confirmed' => 'La confirmación de la nueva contraseña no coincide',
            'password_nueva_confirmation.required' => 'La confirmación de la nueva contraseña es requerida',
            'password_nueva_confirmation.min' => 'La confirmación de la nueva contraseña debe tener al menos 8 caracteres',
        ];
    }
}
