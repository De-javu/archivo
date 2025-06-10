<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCarpetaRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    
return [
    'carpeta' => 'required|exists:carpetas,id', // Asegura que se envíe un ID de carpeta válido
    'nombre' => [
        'required',
        function ($attribute, $value, $fail) {
            $carpetaId = $this->request->get('carpeta');
            $carpeta = \App\Models\Carpeta::find($carpetaId); // Obtiene la carpeta por ID
            if ($carpeta && $value !== $carpeta->nombre) {
                $fail('El nombre de la carpeta no coincide con el nombre de la carpeta seleccionada para eliminar.');
            }
        },
    ]
];
    }
}
