<?php

declare(strict_types=1);

namespace src\cambios\domain\contracts;

/**
 * Cuerpo del correo de avisos de cambios en actividades (una fila por cambio).
 */
interface AvisosCambiosMailCuerpoInterface
{
    /**
     * @param list<string> $cabeceras
     * @param array<int, array<int, string>> $filas cada fila con las celdas en el orden de las cabeceras (índices 1..n).
     */
    public function cuerpo(string $titulo, array $cabeceras, array $filas): string;
}
