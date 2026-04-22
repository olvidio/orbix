<?php

namespace src\notas\application;

use src\notas\application\support\PersonaNotaInputParser;

/**
 * Edita una `PersonaNota` existente. Ataca siempre a la tabla padre
 * `e_notas` (la clase `EditarPersonaNota` ya resuelve el repositorio
 * correcto en funcion del tipo de acta).
 */
final class PersonaNotaEditar
{
    public static function execute(array $input): string
    {
        try {
            $oPersonaNota = PersonaNotaInputParser::parse($input);
            $oEditar = new EditarPersonaNota($oPersonaNota);
            $id_asignatura_real = (int)($input['id_asignatura_real'] ?? 0);
            $oEditar->editar($id_asignatura_real);
            return '';
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }
}
