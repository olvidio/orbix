<?php

declare(strict_types=1);

/**
 * Helpers de lectura de entrada compatibles con el despacho in-process.
 *
 * `filter_post(...)` / `filter_get(...)` leen de la
 * copia interna que el SAPI hace UNA sola vez al inicio del request (a partir
 * del cuerpo/query HTTP real) y NO reflejan cambios en las superglobales
 * `$_POST` / `$_GET`. Esa copia es inmutable desde userland.
 *
 * `frontend\shared\PostRequest::dispatchInProcess` ejecuta controladores
 * `/src/...` en el MISMO proceso reescribiendo `$_POST` y `$_GET` con los
 * parámetros de la llamada interna (para no agotar workers PHP-FPM con HTTP
 * anidado). En ese contexto `filter_input` devolvería los datos del request
 * HTTP exterior (la pantalla), no los de la llamada interna → valores vacíos.
 *
 * Estas funciones replican la semántica de `filter_input` pero leyendo de las
 * superglobales, de modo que se comportan igual en request HTTP directo,
 * in-process y CLI (PHPUnit, donde `filter_input(INPUT_POST)` siempre es null).
 *
 * Estas funciones se definen en el espacio de nombres global a propósito: son
 * un reemplazo directo (drop-in) de la función nativa `filter_input`, que las
 * llamadas sin cualificar de cualquier namespace resuelven por fallback global.
 */

if (!function_exists('filter_from_superglobal')) {
    /**
     * Equivalente a {@see filter_input} pero leyendo de la superglobal indicada.
     *
     * Igual que `filter_input`: si la variable no existe devuelve `null`; si
     * existe pero el filtro falla devuelve `false` (o `null` con
     * `FILTER_NULL_ON_FAILURE`, gestionado por {@see filter_var}).
     *
     * @param array<int|string, mixed> $source
     * @param array<string, mixed>|int $options
     */
    function filter_from_superglobal(
        array $source,
        string $name,
        int $filter = FILTER_DEFAULT,
        array|int $options = 0
    ): mixed {
        if (!array_key_exists($name, $source)) {
            return null;
        }

        return filter_var($source[$name], $filter, $options);
    }
}

if (!function_exists('filter_post')) {
    /**
     * Reemplazo in-process-safe de `filter_post(...)`.
     *
     * @param array<string, mixed>|int $options
     */
    function filter_post(string $name, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        /** @var array<int|string, mixed> $post */
        $post = $_POST;

        return filter_from_superglobal($post, $name, $filter, $options);
    }
}

if (!function_exists('filter_get')) {
    /**
     * Reemplazo in-process-safe de `filter_get(...)`.
     *
     * @param array<string, mixed>|int $options
     */
    function filter_get(string $name, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        /** @var array<int|string, mixed> $get */
        $get = $_GET;

        return filter_from_superglobal($get, $name, $filter, $options);
    }
}
