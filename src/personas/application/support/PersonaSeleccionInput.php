<?php

namespace src\personas\application\support;

/**
 * Extrae id_nom e id_tabla del POST típico de listados (campo sel o campos sueltos).
 */
final class PersonaSeleccionInput
{
    /**
     * @param array<string, mixed> $input
     * @return array{id_nom: int, id_tabla: string}
     */
    public static function idNomYTabla(array $input): array
    {
        $sel = $input['sel'] ?? null;
        if (is_array($sel) && $sel !== []) {
            $primero = (string)($sel[0] ?? '');
            if ($primero !== '') {
                $id_nom = (int)strtok($primero, '#');
                $id_tabla = (string)strtok('#');

                return ['id_nom' => $id_nom, 'id_tabla' => $id_tabla];
            }
        }
        if (is_string($sel) && $sel !== '') {
            $id_nom = (int)strtok($sel, '#');
            $id_tabla = (string)strtok('#');

            return ['id_nom' => $id_nom, 'id_tabla' => $id_tabla];
        }

        return [
            'id_nom' => (int)($input['id_nom'] ?? 0),
            'id_tabla' => (string)($input['id_tabla'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function idNom(array $input): int
    {
        return self::idNomYTabla($input)['id_nom'];
    }
}
