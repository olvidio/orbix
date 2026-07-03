<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;

/**
 * Loader (lazy + cacheado) de los metadatos que necesita
 * {@see TiposDeActividades} para funcionar sin repositorio:
 *
 *  - `maps`: los 4 mapas texto→código del id_tipo_activ (sfsv, asistentes,
 *    actividad 1 dígito, actividad 2 dígitos). Vienen del backend para no
 *    duplicar en frontend las constantes que ya viven en
 *    {@see \src\actividades\domain\entity\TiposActividades}.
 *  - `filas`: la lista plana `{id_tipo_activ, nombre}` de `a_tipos_actividad`,
 *    contra la que se resuelven en memoria los "posibles" sin tocar el
 *    repositorio en cada lookup.
 *
 * Estrategia de cache (dos niveles):
 *
 *  1. Estática por proceso (`self::$cache`): evita reentrar en `$_SESSION`
 *     dentro de la misma request cuando se construyen varios
 *     {@see TiposDeActividades}.
 *  2. Por sesión de usuario (`$_SESSION[self::SESSION_KEY]`): se popula la
 *     primera vez en la sesión y se reutiliza en todas las peticiones
 *     posteriores. Como `a_tipos_actividad` solo cambia con operaciones
 *     admin, la cache se invalida explícitamente desde
 *     `TipoActivNuevo` / `TipoActivUpdate` / `TipoActivEliminar` mediante
 *     {@see self::forget()} tras la escritura.
 *
 * Si alguna vez cambia la forma del payload, **basta con bumpear**
 * {@see self::SESSION_KEY} (p. ej. `_v2`) para que las sesiones vivas
 * descarten su entrada vieja.
 */
final class TipoActivMetadataLoader
{
    /**
     * Clave bajo `$_SESSION` donde se persiste el payload entre requests.
     * Incluye sufijo de versión: bumpear si cambia la forma de los datos.
     */
    private const SESSION_KEY = 'tipo_activ_metadata_v1';

    /**
     * @var array{
     *     maps: array{
     *         sfsv: array<string, int|string>,
     *         asistentes: array<string, int|string>,
     *         actividad1digito: array<string, int|string>,
     *         actividad2digitos: array<string, int|string>,
     *     },
     *     filas: list<array{id_tipo_activ:int, nombre:string}>,
     * }|null
     */
    private static ?array $cache = null;

    /**
     * Mapas texto→código (sfsv/asistentes/actividad1digito/actividad2digitos).
     *
     * @return array{
     *     sfsv: array<string, int|string>,
     *     asistentes: array<string, int|string>,
     *     actividad1digito: array<string, int|string>,
     *     actividad2digitos: array<string, int|string>,
     * }
     */
    public static function maps(): array
    {
        return self::load()['maps'];
    }

    /**
     * Filas planas de `a_tipos_actividad` reducidas a `{id_tipo_activ, nombre}`.
     *
     * @return list<array{id_tipo_activ:int, nombre:string}>
     */
    public static function filas(): array
    {
        return self::load()['filas'];
    }

    /**
     * Inyecta un payload pre-cargado (uso interno y tests). Útil cuando el
     * llamador ya tiene los datos (p. ej. en un test) y quiere evitar el
     * roundtrip HTTP.
     *
     * @param array{
     *     maps: array{
     *         sfsv: array<string, int|string>,
     *         asistentes: array<string, int|string>,
     *         actividad1digito: array<string, int|string>,
     *         actividad2digitos: array<string, int|string>,
     *     },
     *     filas: list<array{id_tipo_activ:int, nombre:string}>,
     * } $payload
     */
    public static function preload(array $payload): void
    {
        self::$cache = $payload;
    }

    /**
     * Invalida la cache (estática y de sesión). Llamar tras crear / editar /
     * eliminar un `tipo_activ` para que la próxima lectura recoja el cambio
     * sin tener que esperar a que el usuario cierre sesión.
     *
     * Pensado para invocarse desde
     * {@see \src\actividades\application\TipoActivNuevo::execute()},
     * {@see \src\actividades\application\TipoActivUpdate::execute()} y
     * {@see \src\actividades\application\TipoActivEliminar::execute()}.
     */
    public static function forget(): void
    {
        self::$cache = null;
        if (isset($_SESSION[self::SESSION_KEY])) {
            unset($_SESSION[self::SESSION_KEY]);
        }
    }

    /**
     * Alias de {@see self::forget()} pensado para tests, donde se quiere
     * limpiar el estado entre escenarios sin que el nombre suene a operación
     * de invalidación de negocio.
     */
    public static function reset(): void
    {
        self::forget();
    }

    /**
     * @return array{
     *     maps: array{
     *         sfsv: array<string, int|string>,
     *         asistentes: array<string, int|string>,
     *         actividad1digito: array<string, int|string>,
     *         actividad2digitos: array<string, int|string>,
     *     },
     *     filas: list<array{id_tipo_activ:int, nombre:string}>,
     * }
     */
    private static function load(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        // Cache de sesión: si ya está poblada, no volvemos a tocar HTTP en
        // toda la sesión hasta que un admin cree/edite/borre un tipo_activ
        // (esos casos de uso llaman a self::forget()).
        $sessionRaw = $_SESSION[self::SESSION_KEY] ?? null;
        if (is_array($sessionRaw)) {
            self::$cache = self::normalize($sessionRaw);

            return self::$cache;
        }

        $payload = PostRequest::getDataFromUrl('/src/actividades/tipo_activ_metadata');
        self::$cache = self::normalize($payload);

        // Persistir en sesión solo si la sesión está activa (en CLI/tests
        // basta con la cache estática y no queremos warnings de PHP).
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION[self::SESSION_KEY] = self::$cache;
        }

        return self::$cache;
    }

    /**
     * Saneado defensivo del payload del backend (estructura mínima esperada).
     *
     * @param array<int|string, mixed> $payload
     * @return array{
     *     maps: array{
     *         sfsv: array<string, int|string>,
     *         asistentes: array<string, int|string>,
     *         actividad1digito: array<string, int|string>,
     *         actividad2digitos: array<string, int|string>,
     *     },
     *     filas: list<array{id_tipo_activ:int, nombre:string}>,
     * }
     */
    private static function normalize(array $payload): array
    {
        $rawMaps = is_array($payload['maps'] ?? null) ? $payload['maps'] : [];
        $rawFilas = is_array($payload['filas'] ?? null) ? $payload['filas'] : [];

        $maps = [
            'sfsv' => self::normalizeMap($rawMaps['sfsv'] ?? null),
            'asistentes' => self::normalizeMap($rawMaps['asistentes'] ?? null),
            'actividad1digito' => self::normalizeMap($rawMaps['actividad1digito'] ?? null),
            'actividad2digitos' => self::normalizeMap($rawMaps['actividad2digitos'] ?? null),
        ];

        $filas = [];
        foreach ($rawFilas as $row) {
            if (!is_array($row)) {
                continue;
            }
            $filas[] = [
                'id_tipo_activ' => PayloadCoercion::int($row['id_tipo_activ'] ?? 0),
                'nombre' => PayloadCoercion::string($row['nombre'] ?? ''),
            ];
        }

        return ['maps' => $maps, 'filas' => $filas];
    }

    /**
     * @return array<string, int|string>
     */
    private static function normalizeMap(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if (is_int($value) || is_string($value)) {
                $out[$key] = $value;
            } else {
                $out[$key] = PayloadCoercion::string($value);
            }
        }

        return $out;
    }
}
