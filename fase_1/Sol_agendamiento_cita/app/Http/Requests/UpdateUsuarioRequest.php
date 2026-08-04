<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
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
        $id = $this->route('id');
        
        return [
            'nombre' => 'sometimes|string|max:255',
            'correo' => [
                'sometimes',
                'email',
                Rule::unique('usuarios', 'correo')->ignore($id),
                'max:255'
            ],
            'password' => 'sometimes|string|min:8',
            'rol' => 'sometimes|in:admin,usuario',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.string' => 'El nombre debe ser una cadena de texto',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'correo.email' => 'El correo debe ser una dirección de correo válida',
            'correo.unique' => 'El correo ya está registrado',
            'correo.max' => 'El correo no puede exceder 255 caracteres',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'rol.in' => 'El rol debe ser: admin o usuario',
        ];
    }
}
