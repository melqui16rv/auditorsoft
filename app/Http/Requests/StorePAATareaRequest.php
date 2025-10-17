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
            'rol_oci' => ['required', 'string', 'in:fomento_cultura,apoyo_fortalecimiento,investigaciones,evaluacion_control,evaluacion_gestion'],
            'nombre' => ['required', 'string', 'min:5', 'max:255'],
            'descripcion' => ['required', 'string', 'min:10', 'max:1000'],
            'fecha_inicio' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
            'auditor_responsable_id' => ['required', 'exists:users,id'],
            'objetivo' => ['nullable', 'string', 'max:2000'],
            'alcance' => ['nullable', 'string', 'max:2000'],
            'criterios_auditoria' => ['nullable', 'string', 'max:2000'],
            'recursos_necesarios' => ['nullable', 'string', 'max:2000'],
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
            'rol_oci.required' => 'El rol OCI es obligatorio.',
            'rol_oci.in' => 'El rol OCI seleccionado no es válido.',
            'nombre.required' => 'El nombre de la tarea es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 5 caracteres.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion.required' => 'La descripción de la tarea es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'auditor_responsable_id.required' => 'El responsable es obligatorio.',
            'auditor_responsable_id.exists' => 'El responsable seleccionado no es válido.',
            'objetivo.max' => 'El objetivo no puede exceder 2000 caracteres.',
            'alcance.max' => 'El alcance no puede exceder 2000 caracteres.',
            'criterios_auditoria.max' => 'Los criterios de auditoría no pueden exceder 2000 caracteres.',
            'recursos_necesarios.max' => 'Los recursos necesarios no pueden exceder 2000 caracteres.',
        ];
    }
}
