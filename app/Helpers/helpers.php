<?php

if (!function_exists('capitalizarPalabra')) {
    /**
     * Función personalizada para capitalizar correctamente palabras con tildes.
     *
     * @param string $string
     * @return string
     */
    function capitalizarPalabra($string)
    {
        return mb_convert_case(mb_strtolower($string), MB_CASE_TITLE, "UTF-8");
    }
}
