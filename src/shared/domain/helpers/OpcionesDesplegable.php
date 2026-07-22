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
            if (array_key_exists('value', $row) && array_key_exists('label', $row)) {
                $value = $row['value'];
                $label = $row['label'];
                $ordenadas[] = [
                    is_scalar($value) ? (string) $value : '',
                    is_scalar($label) ? (string) $label : '',
                ];
                continue;
            }
            $vals = array_values($row);
            if (count($vals) < 2) {
                continue;
            }
            $ordenadas[] = [
                is_scalar($vals[0]) ? (string) $vals[0] : '',
                is_scalar($vals[1]) ? (string) $vals[1] : '',
            ];
        }

        return $ordenadas;
    }
}
