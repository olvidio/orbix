<?php

namespace src\notas\application;

use src\notas\application\support\PersonaNotaInputParser;

/**
 * Elimina una `PersonaNota` a traves de la tabla padre `e_notas`.
 */
final class PersonaNotaEliminar
{
    public static function execute(array $input): string
    {
        try {
            $oPersonaNota = PersonaNotaInputParser::parse($input, eliminar: true);
            $oEditar = new EditarPersonaNota($oPersonaNota);
            $oEditar->eliminar();
            return '';
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }
}
