<?php

declare(strict_types=1);

namespace src\actividadestudios\application\support;

/**
 * Token `sel` del dossier 3005: id_activ#id_asignatura#editable#id_schema.
 *
 * Sin `id_schema` dos asignaturas iguales (dl organizadora y otra dl) colisionan
 * y el acta/matrícula se resuelven por la primera fila de `*_all`.
 */
final class ActividadAsignaturaSelToken
{
    /**
     * @return array{id_activ: int, id_asignatura: int, editable: bool, id_schema: int}
     */
    public static function decode(string $token): array
    {
        $parts = explode('#', $token);

        return [
            'id_activ' => self::intPart($parts[0] ?? ''),
            'id_asignatura' => self::intPart($parts[1] ?? ''),
            'editable' => ($parts[2] ?? '') === 'true',
            'id_schema' => self::intPart($parts[3] ?? ''),
        ];
    }

    public static function encode(int $idActiv, int $idAsignatura, bool $editable, int $idSchema): string
    {
        return $idActiv . '#' . $idAsignatura . '#' . ($editable ? 'true' : 'false') . '#' . $idSchema;
    }

    private static function intPart(string $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }
}
