<?php

declare(strict_types=1);

namespace src\shared\infrastructure\ui\http;

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Vigencia de la sesión de un usuario.
 *
 * - Caduca tras un tiempo sin peticiones (`ORBIX_SESION_INACTIVIDAD_MIN`, 120 minutos por defecto) y, en
 *   cualquier caso, a las `ORBIX_SESION_MAXIMA_HORAS` (12 por defecto) de haber entrado.
 * - Cada pocos minutos se comprueba que el usuario sigue existiendo y que no le han cambiado la
 *   contraseña ni el rol: si es así, la sesión se cierra (un usuario borrado no sigue dentro, y cambiar
 *   la contraseña cierra las sesiones abiertas en otros sitios).
 *
 * Una sesión caducada queda vacía; el arranque la trata como la de un visitante sin autenticar.
 */
final class SesionVigencia
{
    /** Clave de `$_SESSION` con las marcas de tiempo y la huella de credenciales. */
    public const CLAVE = 'sesion_vigencia';

    /** Segundos entre dos comprobaciones del usuario en la BD. */
    public const INTERVALO_REVALIDACION = 300;

    /**
     * Comprueba la caducidad de la sesión abierta y anota la actividad. Devuelve `false` si ha caducado
     * (y la deja vacía).
     */
    public static function alArrancar(?int $ahora = null): bool
    {
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            return true;
        }
        $ahora ??= time();
        $v = is_array($_SESSION[self::CLAVE] ?? null) ? $_SESSION[self::CLAVE] : [];
        $inicio = is_int($v['inicio'] ?? null) ? $v['inicio'] : $ahora;
        $actividad = is_int($v['actividad'] ?? null) ? $v['actividad'] : $ahora;
        if ($ahora - $actividad > self::inactividadMaxima() || $ahora - $inicio > self::duracionMaxima()) {
            self::cerrar();

            return false;
        }
        $v['inicio'] = $inicio;
        $v['actividad'] = $ahora;
        $_SESSION[self::CLAVE] = $v;

        return true;
    }

    /**
     * @param array<string, mixed>|null $entorno para los tests; por defecto, el entorno del proceso
     */
    public static function inactividadMaxima(?array $entorno = null): int
    {
        return self::entero('ORBIX_SESION_INACTIVIDAD_MIN', 120, $entorno) * 60;
    }

    /**
     * @param array<string, mixed>|null $entorno
     */
    public static function duracionMaxima(?array $entorno = null): int
    {
        return self::entero('ORBIX_SESION_MAXIMA_HORAS', 12, $entorno) * 3600;
    }

    /** Huella de lo que, si cambia, invalida la sesión: el hash de la contraseña y el rol. */
    public static function huella(string $hashPassword, int $idRole): string
    {
        return hash('sha256', $hashPassword . '|' . $idRole);
    }

    /**
     * Comprueba, como mucho cada {@see INTERVALO_REVALIDACION} segundos, que el usuario sigue siendo el
     * mismo. `$huellaActual` devuelve la huella del usuario, o `null` si ya no existe. Devuelve `false`
     * si la sesión se ha cerrado.
     *
     * @param callable(int): ?string $huellaActual
     */
    public static function revalidar(callable $huellaActual, ?int $ahora = null): bool
    {
        $auth = $_SESSION['session_auth'] ?? null;
        if (!is_array($auth) || !is_numeric($auth['id_usuario'] ?? null)) {
            return true;
        }
        $ahora ??= time();
        $v = is_array($_SESSION[self::CLAVE] ?? null) ? $_SESSION[self::CLAVE] : [];
        $ultima = is_int($v['revalidada'] ?? null) ? $v['revalidada'] : null;
        $guardada = is_string($v['huella'] ?? null) ? $v['huella'] : null;
        if ($ultima !== null && $guardada !== null && $ahora - $ultima < self::INTERVALO_REVALIDACION) {
            return true;
        }
        $actual = $huellaActual((int) $auth['id_usuario']);
        if ($actual === null || ($guardada !== null && !hash_equals($guardada, $actual))) {
            self::cerrar();

            return false;
        }
        $v['huella'] = $actual;
        $v['revalidada'] = $ahora;
        $_SESSION[self::CLAVE] = $v;

        return true;
    }

    /**
     * El usuario ha cambiado su propia contraseña en esta sesión: la huella se vuelve a tomar en la
     * siguiente comprobación (esta sesión sigue; las demás, con la huella antigua, se cierran).
     */
    public static function olvidarHuella(): void
    {
        if (is_array($_SESSION[self::CLAVE] ?? null)) {
            unset($_SESSION[self::CLAVE]['huella'], $_SESSION[self::CLAVE]['revalidada']);
        }
    }

    /**
     * Revalida al usuario de la sesión contra la BD (con el contenedor ya montado). Si la sesión se
     * cierra, responde como a un visitante sin autenticar y termina la petición.
     */
    public static function revalidarUsuarioDeLaSesion(): void
    {
        if (PHP_SAPI === 'cli' || session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        try {
            $vigente = self::revalidar(static function (int $id): ?string {
                $usuario = DependencyResolver::get(UsuarioRepositoryInterface::class)->findById($id);

                return $usuario === null
                    ? null
                    : self::huella((string) $usuario->getPasswordAsString(), (int) $usuario->getId_role());
            });
        } catch (\Throwable $e) {
            // Si la BD no responde no se echa a nadie: se reintenta en la siguiente petición.
            error_log('[SesionVigencia] ' . $e->getMessage());

            return;
        }
        if ($vigente) {
            return;
        }
        session_write_close();
        http_response_code(401);
        header('X-Orbix-Auth-Required: 1');
        $uri = is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : '';
        $esApi = str_contains((string) parse_url($uri, PHP_URL_PATH), '/src/')
            || (is_string($_GET['r'] ?? null) && str_starts_with($_GET['r'], '/src/'));
        $mensaje = _("La sesión se ha cerrado. Vuelva a entrar.");
        if ($esApi) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'mensaje' => $mensaje, 'data' => ['code' => 'auth_required']], JSON_UNESCAPED_UNICODE);
        } else {
            echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
        }
        exit;
    }

    private static function cerrar(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }
    }

    /**
     * @param array<string, mixed>|null $entorno
     */
    private static function entero(string $nombre, int $porDefecto, ?array $entorno): int
    {
        $valor = $entorno !== null ? ($entorno[$nombre] ?? null) : ($_ENV[$nombre] ?? getenv($nombre));

        return is_numeric($valor) && (int) $valor > 0 ? (int) $valor : $porDefecto;
    }
}
