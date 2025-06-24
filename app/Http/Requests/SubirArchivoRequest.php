<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubirArchivoRequest extends FormRequest
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
            'carpeta_id' => 'required|exists:carpetas,id', // Asegura que se envíe un ID de carpeta válido
            'carpeta_nombre' => 'required|string|max:255', // Asegura que se envíe un nombre de carpeta válido
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,xlsx,docx,mp3,mp4|max:9048' // Asegura que se envíe un archivo válido con un tamaño máximo de 5MB y tipos permitidos
        ];
    }
}
