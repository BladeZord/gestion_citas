<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCitaRequest extends FormRequest
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
            'user_id' => 'required|exists:usuarios,id',
            'tipo_cita_id' => 'required|exists:tipos_cita,id',
            'fecha_cita' => 'required|date|after_or_equal:today',
            'hora_cita' => 'required|date_format:H:i',
            'estado' => 'nullable|in:pendiente,confirmada,cancelada',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario no existe',
            'tipo_cita_id.required' => 'El tipo de cita es obligatorio',
            'tipo_cita_id.exists' => 'El tipo de cita no existe',
            'fecha_cita.required' => 'La fecha de la cita es obligatoria',
            'fecha_cita.date' => 'La fecha debe ser una fecha válida',
            'fecha_cita.after_or_equal' => 'La fecha debe ser hoy o una fecha futura',
            'hora_cita.required' => 'La hora de la cita es obligatoria',
            'hora_cita.date_format' => 'La hora debe tener el formato HH:mm',
            'estado.in' => 'El estado debe ser: pendiente, confirmada o cancelada',
        ];
    }
}

