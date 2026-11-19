<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use Dotenv\Dotenv;

/**
 * Carga el `.env` de un directorio (`src/shared/load_env.php` lo llama con la raíz del proyecto).
 * Sin excepción si falta el fichero.
 *
 * Las variables quedan en `$_ENV`, `$_SERVER` y también en `getenv()` (`createUnsafeImmutable`: con
 * `putenv`), porque buena parte del código las lee con `getenv()`. Inmutable: una variable que ya viene
 * del entorno (nginx `fastcgi_param`, la línea de órdenes) no se sustituye por la del `.env`.
 */
final class CargarEnv
{
    public static function desde(string $dir): void
    {
        if (!class_exists(Dotenv::class)) {
            return;
        }
        Dotenv::createUnsafeImmutable($dir)->safeLoad();
    }
}
