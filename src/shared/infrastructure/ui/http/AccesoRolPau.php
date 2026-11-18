<?php

declare(strict_types=1);

namespace src\shared\infrastructure\ui\http;

/**
 * Lo que pueden pedir desde el navegador los usuarios con un rol limitado a una persona, casa o centro (rol
 * «pau» de `aux_roles`: `sacd`, `cdc`, `ctr`): solo lo de `src/shared/config/acceso_rol_pau.php`. Los
 * usuarios de oficina (sin pau) no se comprueban. Revisión de seguridad del 2026-11-18, punto 2: antes un
 * usuario de una casa o de un centro, que entra desde fuera, llegaba a cualquier pantalla o endpoint.
 *
 * Se llama desde los dos puntos por los que entra una petición del navegador (`FrontBootstrap` y
 * `after_global_object.inc`); las llamadas internas de `PostRequest` no pasan por ellos.
 *
 * - Lo que no está en la lista se apunta siempre en `log/` + {@see REGISTRO_DENEGADO} (pau, método, ruta y
 *   nombres de los parámetros, nunca sus valores). Solo se corta si `ORBIX_ACCESO_PAU=exigir` (`.env`); si
 *   no, de momento solo se avisa, hasta comprobar con el uso real que la lista no deja fuera nada.
 * - Con `log/` + {@see BANDERA_REGISTRO} se apunta además todo lo que piden, en {@see REGISTRO}.
 */
final class AccesoRolPau
{
    public const BANDERA_REGISTRO = 'acceso_pau_registrar';
    public const REGISTRO = 'acceso_pau.log';
    public const REGISTRO_DENEGADO = 'acceso_pau_denegado.log';
    private const PROXY_AJAX = 'frontend/shared/controller/src_ajax.php/';

    /** @var array{comunes: list<string>, por_pau: array<string, list<string>>}|null */
    private static ?array $lista = null;

    /**
     * Comprueba la petición actual. Si el usuario no puede pedirla y se exige, responde «Operación no
     * autorizada» (JSON en `/src`, texto en una pantalla) y termina.
     */
    public static function comprobarPeticionActual(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? null;
        if (!is_string($uri) || PHP_SAPI === 'cli') {
            return;
        }
        $auth = $_SESSION['session_auth'] ?? null;
        $pau = is_array($auth) && is_string($auth['role_pau'] ?? null) ? $auth['role_pau'] : '';
        if ($pau === '') {
            return;
        }
        $metodo = is_string($_SERVER['REQUEST_METHOD'] ?? null) ? $_SERVER['REQUEST_METHOD'] : '';
        $parametros = $_POST !== [] ? $_POST : $_GET;
        $dirLog = dirname(__DIR__, 5) . '/log';
        self::registrar($dirLog, $pau, $metodo, $uri, $parametros);
        if (self::decidir($dirLog, $pau, $metodo, $uri, $parametros, self::exigir())) {
            return;
        }
        http_response_code(403);
        $mensaje = _("Operación no autorizada");
        if (str_starts_with(self::rutaEfectiva(self::ruta($uri)), 'src/')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'mensaje' => $mensaje, 'data' => 'none'], JSON_UNESCAPED_UNICODE);
        } else {
            echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
        }
        exit;
    }

    /**
     * Si la petición puede seguir. Lo que el pau no puede pedir se apunta; se deja pasar si no se exige.
     *
     * @param array<array-key, mixed> $parametros
     */
    public static function decidir(string $dirLog, string $pau, string $metodo, string $requestUri, array $parametros, bool $exigir): bool
    {
        $ruta = self::ruta($requestUri);
        if (self::permite($pau, $ruta)) {
            return true;
        }
        self::apuntar($dirLog . '/' . self::REGISTRO_DENEGADO, $pau, $metodo, $ruta, $parametros);

        return !$exigir;
    }

    /** Si un usuario con ese pau (vacío: de oficina) puede pedir esa ruta (ver {@see ruta()}). */
    public static function permite(string $pau, string $ruta): bool
    {
        if ($pau === '') {
            return true;
        }
        $lista = self::lista();
        $ruta = self::rutaEfectiva($ruta);
        foreach ([...$lista['comunes'], ...($lista['por_pau'][$pau] ?? [])] as $patron) {
            if ($patron === $ruta || (str_ends_with($patron, '*') && str_starts_with($ruta, substr($patron, 0, -1)))) {
                return true;
            }
        }

        return false;
    }

    /** `ORBIX_ACCESO_PAU=exigir` en `.env` (o en el entorno). */
    private static function exigir(): bool
    {
        return ($_ENV['ORBIX_ACCESO_PAU'] ?? getenv('ORBIX_ACCESO_PAU')) === 'exigir';
    }

    /** Por el proxy AJAX cuenta el endpoint al que se llama. */
    private static function rutaEfectiva(string $ruta): string
    {
        return str_starts_with($ruta, self::PROXY_AJAX) ? substr($ruta, strlen(self::PROXY_AJAX)) : $ruta;
    }

    /** @return array{comunes: list<string>, por_pau: array<string, list<string>>} */
    private static function lista(): array
    {
        if (self::$lista === null) {
            /** @var array{comunes: list<string>, por_pau: array<string, list<string>>} $lista */
            $lista = require dirname(__DIR__, 3) . '/config/acceso_rol_pau.php';
            self::$lista = $lista;
        }

        return self::$lista;
    }

    /** @param array<array-key, mixed> $parametros */
    public static function registrar(string $dirLog, string $pau, string $metodo, string $requestUri, array $parametros): void
    {
        if ($pau === '' || !is_file($dirLog . '/' . self::BANDERA_REGISTRO)) {
            return;
        }
        self::apuntar($dirLog . '/' . self::REGISTRO, $pau, $metodo, self::ruta($requestUri), $parametros);
    }

    /** @param array<array-key, mixed> $parametros */
    private static function apuntar(string $fichero, string $pau, string $metodo, string $ruta, array $parametros): void
    {
        $nombres = array_map('strval', array_keys($parametros));
        sort($nombres);
        $linea = date('c') . "\t" . $pau . "\t" . $metodo . "\t" . $ruta . "\t" . implode(',', $nombres) . "\n";
        @file_put_contents($fichero, $linea, FILE_APPEND | LOCK_EX);
    }

    /** Ruta sin el prefijo de la instalación ni el esquema: desde `frontend/` o `src/`, o el nombre del fichero. */
    public static function ruta(string $requestUri): string
    {
        $path = (string) parse_url($requestUri, PHP_URL_PATH);
        foreach (['/frontend/', '/src/'] as $raiz) {
            $pos = strpos($path, $raiz);
            if ($pos !== false) {
                return substr($path, $pos + 1);
            }
        }

        return basename($path);
    }
}
