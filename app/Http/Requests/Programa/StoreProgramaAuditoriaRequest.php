<?php

namespace App\Http\Requests\Programa;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramaAuditoriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_administrador', 'jefe_auditor']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:200',
            'matriz_priorizacion_id' => 'required|integer|exists:matriz_priorizacion,id',
            'fecha_inicio_programacion' => 'required|date|date_format:Y-m-d',
            'fecha_fin_programacion' => 'required|date|date_format:Y-m-d|after:fecha_inicio_programacion',
            'numero_auditores' => 'nullable|integer|min:1|max:50',
            'objetivos' => 'nullable|string|max:2000',
            'alcance' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del programa es requerido.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'matriz_priorizacion_id.required' => 'Debe seleccionar una matriz de priorización.',
            'matriz_priorizacion_id.exists' => 'La matriz seleccionada no existe.',
            'fecha_inicio_programacion.required' => 'La fecha de inicio es requerida.',
            'fecha_inicio_programacion.date_format' => 'La fecha de inicio debe ser válida.',
            'fecha_fin_programacion.required' => 'La fecha de fin es requerida.',
            'fecha_fin_programacion.date_format' => 'La fecha de fin debe ser válida.',
            'fecha_fin_programacion.after' => 'La fecha de fin debe ser posterior a la de inicio.',
            'numero_auditores.integer' => 'El número de auditores debe ser un valor entero.',
            'numero_auditores.min' => 'El número mínimo de auditores es 1.',
            'numero_auditores.max' => 'El número máximo de auditores es 50.',
            'objetivos.max' => 'Los objetivos no pueden exceder 2000 caracteres.',
            'alcance.max' => 'El alcance no puede exceder 2000 caracteres.',
        ];
    }
}
