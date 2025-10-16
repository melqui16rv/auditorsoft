<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePAASeguimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'descripcion_punto_control' => 'sometimes|string|min:10|max:1000',
            'fecha_seguimiento' => 'sometimes|date',
            'ente_control_id' => 'sometimes|exists:cat_entes_control,id',
            'estado' => 'sometimes|in:pendiente,realizado,anulado',
            'evaluacion' => 'nullable|in:bien,mal,pendiente',
            'observaciones' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'descripcion_punto_control.min' => 'La descripción debe tener al menos 10 caracteres.',
            'descripcion_punto_control.max' => 'La descripción no puede exceder 1000 caracteres.',
            'fecha_seguimiento.date' => 'La fecha del seguimiento debe ser una fecha válida.',
            'ente_control_id.exists' => 'El ente de control seleccionado no existe.',
            'estado.in' => 'El estado debe ser: pendiente, realizado o anulado.',
            'evaluacion.in' => 'La evaluación debe ser: bien, mal o pendiente.',
            'observaciones.max' => 'Las observaciones no pueden exceder 2000 caracteres.',
        ];
    }
}
