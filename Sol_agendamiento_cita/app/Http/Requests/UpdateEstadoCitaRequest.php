<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoCitaRequest extends FormRequest
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
            'estado' => 'required|in:pendiente,confirmada,cancelada',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser: pendiente, confirmada o cancelada',
        ];
    }
}

