<?php

declare(strict_types=1);

namespace src\shared\domain\helpers;

/**
 * Comprobación mínima de que un fichero subido es un PDF: la cabecera `%PDF-`, que según la
 * especificación puede ir precedida de basura hasta el byte 1024.
 */
final class DocumentoPdf
{
    public static function esPdf(string $contenido): bool
    {
        return str_contains(substr($contenido, 0, 1024), '%PDF-');
    }
}
