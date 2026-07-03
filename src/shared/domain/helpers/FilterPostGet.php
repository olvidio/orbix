<?php

declare(strict_types=1);

namespace src\shared\domain\helpers;

/**
 * Lectura de entrada compatible con el despacho in-process.
 *
 * Equivalente a {@see filter_input} pero leyendo de {@see $_POST} / {@see $_GET},
 * de modo que refleja reescrituras en {@see \frontend\shared\PostRequest::dispatchInProcess}.
 */
final class FilterPostGet
{
    /**
     * Equivalente a {@see filter_input} pero leyendo de la superglobal indicada.
     *
     * @param array<int|string, mixed> $source
     * @param array<string, mixed>|int $options
     */
    public static function fromSuperglobal(
        array $source,
        string $name,
        int $filter = FILTER_DEFAULT,
        array|int $options = 0,
    ): mixed {
        if (!array_key_exists($name, $source)) {
            return null;
        }

        return filter_var($source[$name], $filter, $options);
    }

    /**
     * @param array<string, mixed>|int $options
     */
    public static function post(string $name, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        /** @var array<int|string, mixed> $post */
        $post = $_POST;

        return self::fromSuperglobal($post, $name, $filter, $options);
    }

    /**
     * @param array<string, mixed>|int $options
     */
    public static function get(string $name, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        /** @var array<int|string, mixed> $get */
        $get = $_GET;

        return self::fromSuperglobal($get, $name, $filter, $options);
    }
}
