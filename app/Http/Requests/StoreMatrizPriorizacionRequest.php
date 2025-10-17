<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatrizPriorizacionRequest extends FormRequest
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
            'descripcion' => 'nullable|string|max:1000',
            'vigencia' => 'required|integer|between:2020,2050',
            'municipio_id' => 'required|exists:cat_municipios_colombia,id',
            'fecha_elaboracion' => 'required|date|before_or_equal:today',
            'procesos' => 'required|array|min:1',
            'procesos.*.proceso_id' => 'required|exists:cat_procesos,id',
            'procesos.*.riesgo_nivel' => 'required|in:extremo,alto,moderado,bajo',
            'procesos.*.requiere_comite' => 'boolean',
            'procesos.*.requiere_entes_reguladores' => 'boolean',
            'procesos.*.fecha_ultima_auditoria' => 'nullable|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la matriz es obligatorio',
            'vigencia.required' => 'La vigencia es obligatoria',
            'municipio_id.required' => 'Debe seleccionar un municipio',
            'fecha_elaboracion.required' => 'La fecha de elaboración es obligatoria',
            'procesos.required' => 'Debe seleccionar al menos un proceso',
            'procesos.*.riesgo_nivel.required' => 'El nivel de riesgo es obligatorio para cada proceso',
        ];
    }
}
