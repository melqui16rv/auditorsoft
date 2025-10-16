<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePAARequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo Jefe Auditor y Super Admin pueden crear PAA
        return auth()->check() && in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vigencia' => ['required', 'integer', 'min:2020', 'max:2050'],
            'fecha_elaboracion' => ['required', 'date'],
            'municipio_id' => ['nullable', 'exists:cat_municipios_colombia,id'],
            'nombre_entidad' => ['nullable', 'string', 'max:255'],
            'imagen_institucional' => ['nullable', 'image', 'max:2048'], // 2MB max
            'observaciones' => ['nullable', 'string'],
            'version_formato' => ['nullable', 'string', 'max:20'],
            'fecha_aprobacion_formato' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'vigencia' => 'vigencia del PAA',
            'fecha_elaboracion' => 'fecha de elaboración',
            'municipio_id' => 'municipio',
            'nombre_entidad' => 'nombre de la entidad',
            'imagen_institucional' => 'logo institucional',
            'version_formato' => 'versión del formato',
            'fecha_aprobacion_formato' => 'fecha de aprobación del formato',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vigencia.required' => 'La vigencia del PAA es obligatoria.',
            'vigencia.integer' => 'La vigencia debe ser un año válido.',
            'vigencia.min' => 'La vigencia no puede ser anterior a 2020.',
            'vigencia.max' => 'La vigencia no puede ser posterior a 2050.',
            'fecha_elaboracion.required' => 'La fecha de elaboración es obligatoria.',
            'fecha_elaboracion.date' => 'La fecha de elaboración debe ser una fecha válida.',
            'municipio_id.exists' => 'El municipio seleccionado no existe.',
            'imagen_institucional.image' => 'El archivo debe ser una imagen.',
            'imagen_institucional.max' => 'La imagen no puede ser mayor a 2MB.',
        ];
    }
}
