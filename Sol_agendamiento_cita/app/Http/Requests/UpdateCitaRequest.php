<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
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
            'user_id' => 'sometimes|exists:usuarios,id',
            'tipo_cita_id' => 'sometimes|exists:tipos_cita,id',
            'fecha_cita' => 'sometimes|date|after_or_equal:today',
            'hora_cita' => 'sometimes|date_format:H:i',
            'estado' => 'sometimes|in:pendiente,confirmada,cancelada',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => 'El usuario no existe',
            'tipo_cita_id.exists' => 'El tipo de cita no existe',
            'fecha_cita.date' => 'La fecha debe ser una fecha válida',
            'fecha_cita.after_or_equal' => 'La fecha debe ser hoy o una fecha futura',
            'hora_cita.date_format' => 'La hora debe tener el formato HH:mm',
            'estado.in' => 'El estado debe ser: pendiente, confirmada o cancelada',
        ];
    }
}

