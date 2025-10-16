<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenciaRequest extends FormRequest
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
            'archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'descripcion' => 'nullable|string|max:500',
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
            'archivo.required' => 'Debe seleccionar un archivo para subir.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.max' => 'El archivo no puede superar 10 MB.',
            'archivo.mimes' => 'El archivo debe ser: PDF, Word, Excel, JPG o PNG.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ];
    }
}
