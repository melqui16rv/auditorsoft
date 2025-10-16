<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePAATareaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo Jefe Auditor, Auditor y Super Admin pueden actualizar tareas
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
            'rol_oci_id' => ['sometimes', 'exists:cat_roles_oci,id'],
            'descripcion_tarea' => ['sometimes', 'string', 'min:10', 'max:1000'],
            'fecha_inicio_planeada' => ['sometimes', 'date'],
            'fecha_fin_planeada' => ['sometimes', 'date', 'after:fecha_inicio_planeada'],
            'fecha_inicio_real' => ['nullable', 'date'],
            'fecha_fin_real' => ['nullable', 'date', 'after:fecha_inicio_real'],
            'responsable_id' => ['sometimes', 'exists:users,id'],
            'estado' => ['sometimes', 'in:pendiente,en_proceso,realizado,anulado,vencido'],
            'evaluacion' => ['nullable', 'in:bien,mal,pendiente'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
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
            'rol_oci_id.exists' => 'El rol OCI seleccionado no es válido.',
            'descripcion_tarea.min' => 'La descripción debe tener al menos 10 caracteres.',
            'descripcion_tarea.max' => 'La descripción no puede exceder 1000 caracteres.',
            'fecha_fin_planeada.after' => 'La fecha de fin planeada debe ser posterior a la fecha de inicio planeada.',
            'fecha_fin_real.after' => 'La fecha de fin real debe ser posterior a la fecha de inicio real.',
            'responsable_id.exists' => 'El responsable seleccionado no es válido.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'evaluacion.in' => 'La evaluación seleccionada no es válida.',
            'observaciones.max' => 'Las observaciones no pueden exceder 2000 caracteres.',
        ];
    }
}
