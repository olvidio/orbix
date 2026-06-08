<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use DI\Container;

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
        $resolved = self::container()->get($id);
        if (!is_object($resolved)) {
            throw new \RuntimeException(sprintf('Service "%s" did not resolve to an object', $id));
        }

        /** @var T $resolved */
        return $resolved;
    }

    /**
     * @param array<string, mixed> $parameters Argumentos para definiciones con `make()`.
     */
    public static function make(string $name, array $parameters = []): mixed
    {
        return self::container()->make($name, $parameters);
    }

    private static function container(): Container
    {
        $container = $GLOBALS['container'] ?? null;
        if (!$container instanceof Container) {
            throw new \RuntimeException('DI container not initialized');
        }

        return $container;
    }
}
