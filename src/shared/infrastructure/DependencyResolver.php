<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

/**
 * Acceso centralizado al contenedor PHP-DI del bootstrap.
 * Evita dispersar `$GLOBALS['container']` fuera de `src/`.
 */
final class DependencyResolver
{
    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public static function get(string $id): object
    {
        return $GLOBALS['container']->get($id);
    }

    /**
     * @param array<string, mixed> $parameters Argumentos para definiciones con `make()`.
     */
    public static function make(string $name, array $parameters = []): mixed
    {
        return $GLOBALS['container']->make($name, $parameters);
    }
}
