<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HashPasswordRequest extends FormRequest
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
            'password' => 'required|string|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'password.required' => 'El password es requerido',
            'password.string' => 'El password debe ser una cadena de texto',
            'password.min' => 'El password debe tener al menos 1 carácter',
        ];
    }
}
