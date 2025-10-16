<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePAATareaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo Jefe Auditor, Auditor y Super Admin pueden crear tareas
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
            'rol_oci_id' => ['required', 'exists:cat_roles_oci,id'],
            'nombre_tarea' => ['required', 'string', 'min:5', 'max:255'],
            'descripcion_tarea' => ['required', 'string', 'min:10', 'max:1000'],
            'fecha_inicio_planeada' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin_planeada' => ['required', 'date', 'after:fecha_inicio_planeada'],
            'responsable_id' => ['required', 'exists:users,id'],
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
            'rol_oci_id.required' => 'El rol OCI es obligatorio.',
            'rol_oci_id.exists' => 'El rol OCI seleccionado no es válido.',
            'nombre_tarea.required' => 'El nombre de la tarea es obligatorio.',
            'nombre_tarea.min' => 'El nombre debe tener al menos 5 caracteres.',
            'nombre_tarea.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion_tarea.required' => 'La descripción de la tarea es obligatoria.',
            'descripcion_tarea.min' => 'La descripción debe tener al menos 10 caracteres.',
            'descripcion_tarea.max' => 'La descripción no puede exceder 1000 caracteres.',
            'fecha_inicio_planeada.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio_planeada.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fecha_fin_planeada.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin_planeada.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'responsable_id.required' => 'El responsable es obligatorio.',
            'responsable_id.exists' => 'El responsable seleccionado no es válido.',
            'observaciones.max' => 'Las observaciones no pueden exceder 2000 caracteres.',
        ];
    }
}
