<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class validNit implements Rule
{
    protected $municipios;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $json = Storage::get('municipios.json');
        $this->municipios = json_decode($json, true);

        if (empty($this->municipios)) {
            throw new \Exception('El archivo JSON no se ha cargado correctamente o está vacío.');
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Remover cualquier caracter no numérico
        $formattedValue = preg_replace('/[^0-9]/', '', $value);

        // Obtener los primeros cuatro dígitos del NIT
        $codigoMunicipio = substr($formattedValue, 0, 4);

        // Verificar si el código de municipio existe en la lista
        foreach ($this->municipios as $municipio) {
            if ($municipio['codigo'] === $codigoMunicipio) {
                return true;
            }
        }
        // verificar si el código de municipio existe en la lista
        throw new \Exception("Código de municipio no encontrado: {$codigoMunicipio}");

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El NIT no pertenece al pais.';
    }
}
