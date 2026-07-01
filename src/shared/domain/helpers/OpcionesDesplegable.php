<?php

namespace src\shared\domain\helpers;

/**
 * Convierte mapas value => label en listas ordenadas para desplegables AJAX.
 *
 * En JSON, un objeto con claves numéricas se reordena en JavaScript por id;
 * el formato array de pares preserva el orden del backend.
 */
final class OpcionesDesplegable
{
    /**
     * @param array<int|string, string> $aOpciones
     * @return list<array{0: string, 1: string}>
     */
    public static function enOrden(array $aOpciones): array
    {
        $ordenadas = [];
        foreach ($aOpciones as $id => $etiqueta) {
            $ordenadas[] = [(string) $id, (string) $etiqueta];
        }

        return $ordenadas;
    }

    /**
     * @param list<array{value?: mixed, label?: mixed}|array{0: mixed, 1: mixed}> $rows
     * @return list<array{0: string, 1: string}>
     */
    public static function desdeFilas(array $rows): array
    {
        $ordenadas = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (array_key_exists('value', $row) && array_key_exists('label', $row)) {
                $ordenadas[] = [(string) $row['value'], (string) $row['label']];
                continue;
            }
            if (count($row) >= 2) {
                $ordenadas[] = [(string) $row[0], (string) $row[1]];
            }
        }

        return $ordenadas;
    }
}
