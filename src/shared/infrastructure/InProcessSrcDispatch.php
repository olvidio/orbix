<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

/**
 * Marca ejecución en proceso de un controlador `/src/...` (p. ej. {@see \frontend\shared\PostRequest}).
 *
 * {@see \src\shared\web\ContestarJson} no debe usar `JsonResponse::send()` en ese contexto:
 * Symfony cierra todos los output buffers y el JSON acaba como respuesta HTTP del navegador.
 */
final class InProcessSrcDispatch
{
    private static int $depth = 0;

    public static function begin(): void
    {
        self::$depth++;
    }

    public static function end(): void
    {
        if (self::$depth > 0) {
            self::$depth--;
        }
    }

    public static function isActive(): bool
    {
        return self::$depth > 0;
    }
}
