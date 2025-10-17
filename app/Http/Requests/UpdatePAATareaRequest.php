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
            'rol_oci' => ['sometimes', 'string', 'in:fomento_cultura,apoyo_fortalecimiento,investigaciones,evaluacion_control,evaluacion_gestion'],
            'nombre' => ['sometimes', 'string', 'min:5', 'max:255'],
            'descripcion' => ['sometimes', 'string', 'min:10', 'max:1000'],
            'fecha_inicio' => ['sometimes', 'date'],
            'fecha_fin' => ['sometimes', 'date', 'after:fecha_inicio'],
            'auditor_responsable_id' => ['sometimes', 'exists:users,id'],
            'estado' => ['sometimes', 'in:pendiente,en_proceso,realizada,anulada'],
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
            'rol_oci.in' => 'El rol OCI seleccionado no es válido.',
            'nombre.min' => 'El nombre debe tener al menos 5 caracteres.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'auditor_responsable_id.exists' => 'El responsable seleccionado no es válido.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'objetivo.max' => 'El objetivo no puede exceder 2000 caracteres.',
            'alcance.max' => 'El alcance no puede exceder 2000 caracteres.',
            'criterios_auditoria.max' => 'Los criterios de auditoría no pueden exceder 2000 caracteres.',
            'recursos_necesarios.max' => 'Los recursos necesarios no pueden exceder 2000 caracteres.',
        ];
    }
}
