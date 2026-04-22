<?php

namespace src\notas\application;

use src\notas\application\support\PersonaNotaInputParser;

/**
 * Crea una nueva `PersonaNota` (con replicacion DL / certificado segun
 * corresponda). Thin wrapper sobre `EditarPersonaNota::nuevo()` que
 * centraliza el parseo de entrada y el manejo de errores.
 */
final class PersonaNotaNueva
{
    public static function execute(array $input): string
    {
        try {
            $oPersonaNota = PersonaNotaInputParser::parse($input);
            $oEditar = new EditarPersonaNota($oPersonaNota);
            $oEditar->nuevo();
            return '';
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }
}
