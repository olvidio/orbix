<?php

namespace src\shared\traits;

/**
 * Utilidades para columnas bytea de PostgreSQL.
 *
 * PDO devuelve los bytea como resources (stream). Este trait centraliza
 * la lectura del stream y la normalización del contenido para garantizar
 * que siempre se trabaja con datos binarios, independientemente de cómo
 * fueron guardados originalmente.
 */
trait HandlesPgBytea
{
    /**
     * Lee un campo bytea devuelto por PDO (puede ser resource o null).
     */
    protected function readByteaField(mixed $field): ?string
    {
        if ($field === null) {
            return null;
        }
        if (is_resource($field)) {
            $contents = stream_get_contents($field);
            fclose($field);
            return $contents === false ? null : $contents;
        }
        if (is_string($field)) {
            return $field;
        }

        return null;
    }

    /**
     * Normaliza datos binarios que pueden haber sido guardados como hex
     * puro (sin prefijo \x) antes de corregir el método de persistencia.
     *
     * - Si el contenido ya es binario válido (empieza con $binaryMagic), lo devuelve tal cual.
     * - Si el contenido es un string hexadecimal puro (legacy sin \x), aplica hex2bin.
     */
    protected function normalizeBytea(?string $data, string $binaryMagic = '%PDF'): ?string
    {
        if ($data === null) {
            return null;
        }
        if (str_starts_with($data, $binaryMagic)) {
            return $data;
        }
        if (ctype_xdigit($data)) {
            $decoded = hex2bin($data);

            return $decoded === false ? null : $decoded;
        }
        return $data;
    }
}
