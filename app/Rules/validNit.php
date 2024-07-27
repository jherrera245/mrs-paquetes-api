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
        // Ruta hacia el archivo JSON con los municipios app/json/municipios.json
        //toma en cuenta la carpeta raiz del proyecto /mrs-paquetes-api.
        $json = file_get_contents(base_path('app/json/municipios.json'));
        // $json = Storage::get('municipios.json');
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
        // Remover cualquier carácter no numérico
        $formattedValue = preg_replace('/[^0-9]/', '', $value);

        // Verificar si el NIT tiene la longitud correcta
        if (strlen($formattedValue) < 13) {
            return false;
        }

        // Obtener los primeros cuatro dígitos del NIT (código de municipio)
        $codigoMunicipio = substr($formattedValue, 0, 4);

        // Verificar si el código de municipio existe en la lista
        $municipioValido = false;
        foreach ($this->municipios as $municipio) {
            if ($municipio['codigo'] === $codigoMunicipio) {
                $municipioValido = true;
                break;
            }
        }

        if (!$municipioValido) {
            throw new \Exception("Código de municipio no encontrado: {$codigoMunicipio}");
        }

        // Verificar la fecha de nacimiento en el NIT
        $dia = intval(substr($formattedValue, 4, 2));
        $mes = intval(substr($formattedValue, 6, 2));
        $anio = intval(substr($formattedValue, 8, 2));

        // Validar día y mes
        if ($dia < 1 || $dia > 31) {
            return false;
        }
        if ($mes < 1 || $mes > 12) {
            return false;
        }

        // Validar año (opcional, dependiendo de los requisitos)
        // Aquí podrías agregar una validación adicional para el año si lo deseas

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El NIT no es válido. Verifique el formato y la fecha de nacimiento.';
    }
}