<?php

declare(strict_types=1);

namespace frontend\misas\helpers;

use frontend\notas\helpers\NotasFormSupport;

/**
 * Opciones de {@see \frontend\shared\web\Desplegable} para el módulo misas.
 */
final class MisasDesplegableSupport
{
    /**
     * @return array<int|string, string>
     */
    public static function opciones(mixed $raw): array
    {
        return NotasFormSupport::desplegableOpciones($raw);
    }
}
