<?php

declare(strict_types=1);

namespace src\actividadestudios\application\support;

/**
 * Una matrícula con nota en un acta ya firmada no se toca; sin nota
 * puede pasar a una segunda convocatoria.
 */
final class MatriculaNotaEstado
{
    public static function tieneNota(?string $notaNum): bool
    {
        return $notaNum !== null && trim($notaNum) !== '';
    }

    /**
     * @param array<string, true> $actasFirmadas
     */
    public static function editable(?string $acta, ?string $notaNum, array $actasFirmadas): bool
    {
        if (!self::tieneNota($notaNum)) {
            return true;
        }
        $actaKey = trim((string) $acta);

        return $actaKey === '' || !isset($actasFirmadas[$actaKey]);
    }
}
