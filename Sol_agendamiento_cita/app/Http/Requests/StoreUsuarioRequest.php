<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo|max:255',
            'password' => 'required|string|min:8',
            'rol' => 'required|in:admin,usuario',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.string' => 'El nombre debe ser una cadena de texto',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'correo.required' => 'El correo es obligatorio',
            'correo.email' => 'El correo debe ser una dirección de correo válida',
            'correo.unique' => 'El correo ya está registrado',
            'correo.max' => 'El correo no puede exceder 255 caracteres',
            'password.required' => 'La contraseña es obligatoria',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'rol.required' => 'El rol es obligatorio',
            'rol.in' => 'El rol debe ser: admin o usuario',
        ];
    }
}
