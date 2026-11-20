<?php

declare(strict_types=1);

namespace src\shared\infrastructure\ui\http;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;

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
 * - En lo permitido, los ids que manda el navegador tienen que ser de su pau ({@see parametroAjeno()}): de
 *   momento, los de un usuario de una casa. Mismo modo aviso / exigir; en el registro se añade el nombre del
 *   parámetro. Los parámetros dentro de una cápsula `HashB` no se miran: los ha firmado el servidor.
 */
final class AccesoRolPau
{
    public const BANDERA_REGISTRO = 'acceso_pau_registrar';
    public const REGISTRO = 'acceso_pau.log';
    public const REGISTRO_DENEGADO = 'acceso_pau_denegado.log';
    private const PROXY_AJAX = 'frontend/shared/controller/src_ajax.php/';
    /** Parámetros con ids de casa de un usuario de una casa (`sSeleccionados`: separados por comas). */
    private const IDS_CASA = ['id_ubi', 'id_cdc', 'sSeleccionados'];
    /** Pantallas en las que `sel[]` es la actividad (`id_activ#…`). */
    private const SEL_ACTIVIDAD = ['frontend/actividades/controller/planning_casa_modificar.php'];

    /** @var array{comunes: list<string>, por_pau: array<string, list<string>>}|null */
    private static ?array $lista = null;

    /** Ruta `/src/...` que el enrutador ha resuelto y va a despachar, si la ha fijado. */
    private static ?string $rutaDespachada = null;

    /**
     * Los enrutadores (`public/index.php`, `src_ajax.php`) dicen aquí qué ruta `/src/...` van a despachar,
     * para que se compruebe esa y no la que aparenta la URL de la petición.
     */
    public static function fijarRutaDespachada(?string $ruta): void
    {
        self::$rutaDespachada = $ruta;
    }

    /** La ruta `/src/...` que el enrutador ha dicho que va a despachar, si lo ha dicho. */
    public static function rutaDespachada(): ?string
    {
        return self::$rutaDespachada;
    }

    /**
     * @param array<array-key, mixed> $server
     */
    public static function uriQueSeComprueba(array $server): ?string
    {
        if (self::$rutaDespachada !== null) {
            return self::$rutaDespachada;
        }
        $uri = $server['REQUEST_URI'] ?? null;

        return is_string($uri) ? $uri : null;
    }

    /**
     * Comprueba la petición actual. Si el usuario no puede pedirla y se exige, responde «Operación no
     * autorizada» (JSON en `/src`, texto en una pantalla) y termina.
     */
    public static function comprobarPeticionActual(): void
    {
        $uri = self::uriQueSeComprueba($_SERVER);
        if ($uri === null || PHP_SAPI === 'cli') {
            return;
        }
        $auth = $_SESSION['session_auth'] ?? null;
        // Contraseña provisional o caducada: hasta que la cambie, el usuario solo puede cambiarla.
        if (is_array($auth) && ($auth['expire'] ?? '') === 1
            && !self::permitidaConCambioDePasswordPendiente(self::rutaEfectiva(self::ruta($uri)))
        ) {
            self::responderNoAutorizada($uri, _("Debe cambiar la contraseña antes de continuar."));
        }
        $pau = is_array($auth) && is_string($auth['role_pau'] ?? null) ? $auth['role_pau'] : '';
        if ($pau === '') {
            return;
        }
        $metodo = is_string($_SERVER['REQUEST_METHOD'] ?? null) ? $_SERVER['REQUEST_METHOD'] : '';
        $dirLog = dirname(__DIR__, 5) . '/log';
        $conjuntos = self::parametrosDeLaPeticion($_POST, $_GET);
        self::registrar($dirLog, $pau, $metodo, $uri, $conjuntos[0]);
        foreach ($conjuntos as $parametros) {
            if (!self::decidir($dirLog, $pau, $metodo, $uri, $parametros, self::seCorta(), self::idsDelPau($auth), self::ubiDeActividad(...))) {
                self::responderNoAutorizada($uri, _("Operación no autorizada"));
            }
        }
    }

    /**
     * Los parámetros que hay que mirar: los del cuerpo y, aparte, los de la query (un id ajeno en la query
     * no se libra por venir acompañado de un cuerpo).
     *
     * @param array<array-key, mixed> $post
     * @param array<array-key, mixed> $get
     * @return non-empty-list<array<array-key, mixed>>
     */
    public static function parametrosDeLaPeticion(array $post, array $get): array
    {
        $conjuntos = array_values(array_filter([$post, $get], static fn (array $p): bool => $p !== []));

        return $conjuntos === [] ? [[]] : $conjuntos;
    }

    private static function responderNoAutorizada(string $uri, string $mensaje): never
    {
        http_response_code(403);
        if (str_starts_with(self::rutaEfectiva(self::ruta($uri)), 'src/')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'mensaje' => $mensaje, 'data' => 'none'], JSON_UNESCAPED_UNICODE);
        } else {
            echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
        }
        exit;
    }

    /**
     * Rutas que puede pedir un usuario con el cambio de contraseña pendiente (`session_auth.expire`): la
     * portada, que le pinta el formulario, y lo que ese formulario usa.
     */
    public static function permitidaConCambioDePasswordPendiente(string $ruta): bool
    {
        // Lo que no es una pantalla ni un endpoint es la portada (`index.php`, o el directorio de la instalación).
        if (!str_starts_with($ruta, 'frontend/') && !str_starts_with($ruta, 'src/')) {
            return true;
        }
        $corta = preg_replace('~^src/([^/]+)/infrastructure/ui/http/controllers/([^/]+)\.php$~', 'src/$1/$2', $ruta);

        return in_array(is_string($corta) ? $corta : $ruta, [
            'frontend/usuarios/controller/usuario_form_pwd.php',
            'src/usuarios/usuario_guardar_pwd',
            'src/usuarios/usuario_guardar_pwd_form_data',
            'src/usuarios/usuario_check_pwd',
        ], true);
    }

    /**
     * Si la petición puede seguir. Lo que el pau no puede pedir (la ruta, o un id que no es suyo) se apunta;
     * se deja pasar si no se exige.
     *
     * @param array<array-key, mixed> $parametros
     * @param list<string> $idsPau ids de su persona, casas o centro (`csv_id_pau`)
     * @param (callable(int): ?int)|null $ubiDeActividad casa (id_ubi) de una actividad
     */
    public static function decidir(string $dirLog, string $pau, string $metodo, string $requestUri, array $parametros, bool $exigir, array $idsPau = [], ?callable $ubiDeActividad = null): bool
    {
        $ruta = self::ruta($requestUri);
        if (!self::permite($pau, $ruta)) {
            self::apuntar($dirLog . '/' . self::REGISTRO_DENEGADO, $pau, $metodo, $ruta, $parametros);

            return !$exigir;
        }
        $ajeno = self::parametroAjeno($pau, $ruta, $parametros, $idsPau, $ubiDeActividad ?? static fn (int $id): ?int => null);
        if ($ajeno === null) {
            return true;
        }
        self::apuntar($dirLog . '/' . self::REGISTRO_DENEGADO, $pau, $metodo, $ruta, $parametros, $ajeno);

        return !$exigir;
    }

    /**
     * El primer parámetro con un id que no es del pau del usuario, o null. De momento solo se mira el de una
     * casa (`cdc`): sus casas, «una casa» (`cdc_sel=9`) con alguna suya, y actividades en sus casas.
     *
     * @param array<array-key, mixed> $parametros
     * @param list<string> $idsPau
     * @param callable(int): ?int $ubiDeActividad
     */
    public static function parametroAjeno(string $pau, string $ruta, array $parametros, array $idsPau, callable $ubiDeActividad): ?string
    {
        if ($pau !== 'cdc') {
            return null;
        }
        $mias = array_flip($idsPau);
        $alguna = false;
        foreach (self::IDS_CASA as $nombre) {
            foreach (self::valores($parametros[$nombre] ?? null, $nombre === 'sSeleccionados') as $valor) {
                if (!isset($mias[$valor])) {
                    return $nombre;
                }
                $alguna = true;
            }
        }
        if (array_key_exists('cdc_sel', $parametros) && ($parametros['cdc_sel'] !== '9' || !$alguna)) {
            return 'cdc_sel';
        }
        $actividades = ['id_activ' => self::valores($parametros['id_activ'] ?? null, false)];
        if (in_array(self::rutaEfectiva($ruta), self::SEL_ACTIVIDAD, true)) {
            $actividades['sel'] = array_map(static fn (string $v): string => strtok($v, '#') ?: '', self::valores($parametros['sel'] ?? null, false));
        }
        foreach ($actividades as $nombre => $ids) {
            foreach ($ids as $id) {
                $ubi = ctype_digit($id) ? $ubiDeActividad((int) $id) : null;
                if ($ubi === null || !isset($mias[(string) $ubi])) {
                    return $nombre;
                }
            }
        }

        return null;
    }

    /**
     * Los valores no vacíos (ni `0`) de un parámetro, escalar o lista.
     *
     * @return list<string>
     */
    private static function valores(mixed $parametro, bool $porComas): array
    {
        $valores = [];
        foreach (is_array($parametro) ? $parametro : [$parametro] as $v) {
            if (!is_scalar($v)) {
                continue;
            }
            foreach ($porComas ? explode(',', (string) $v) : [(string) $v] as $parte) {
                $parte = trim($parte);
                if ($parte !== '' && $parte !== '0') {
                    $valores[] = $parte;
                }
            }
        }

        return $valores;
    }

    /**
     * Ids de su persona, casas o centro (`csv_id_pau` del usuario en sesión).
     *
     * @return list<string>
     */
    private static function idsDelPau(mixed $auth): array
    {
        $usuario = is_array($auth) ? ($auth['MiUsuario'] ?? null) : null;
        $csv = is_object($usuario) && method_exists($usuario, 'getCsvIdPauAsString') ? $usuario->getCsvIdPauAsString() : null;

        return is_string($csv) ? self::valores($csv, true) : [];
    }

    private static function ubiDeActividad(int $idActiv): ?int
    {
        $container = $GLOBALS['container'] ?? null;
        if (!is_object($container) || !method_exists($container, 'get')) {
            return null;
        }
        $repositorio = $container->get(ActividadAllRepositoryInterface::class);
        $actividad = $repositorio instanceof ActividadAllRepositoryInterface ? $repositorio->findById($idActiv) : null;

        return $actividad?->getId_ubi();
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

    /**
     * Lo que queda fuera de la lista se corta, salvo con `ORBIX_ACCESO_PAU=aviso` en `.env` (o en el
     * entorno), que solo lo apunta: sirve para completar la lista en una instalación antes de exigirla.
     */
    public static function seCorta(): bool
    {
        return ($_ENV['ORBIX_ACCESO_PAU'] ?? getenv('ORBIX_ACCESO_PAU')) !== 'aviso';
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
    private static function apuntar(string $fichero, string $pau, string $metodo, string $ruta, array $parametros, string $ajeno = ''): void
    {
        $nombres = array_map('strval', array_keys($parametros));
        sort($nombres);
        $linea = date('c') . "\t" . $pau . "\t" . $metodo . "\t" . $ruta . "\t" . implode(',', $nombres) . ($ajeno !== '' ? "\t" . $ajeno : '') . "\n";
        @file_put_contents($fichero, $linea, FILE_APPEND | LOCK_EX);
    }

    /**
     * Ruta sin el prefijo de la instalación ni el esquema: desde `frontend/` o `src/`, o el nombre del fichero.
     * Es la ruta que va a despachar el enrutador, no la que aparenta la URL: un `r=/src/...` en la query
     * manda sobre el path (como en `public/index.php`; si hay varios, el último, como en PHP) y los `.` y
     * `..` se resuelven antes de comparar con los patrones.
     */
    public static function ruta(string $requestUri): string
    {
        $query = parse_url($requestUri, PHP_URL_QUERY);
        if (is_string($query) && $query !== '') {
            parse_str($query, $parametros);
            $r = $parametros['r'] ?? null;
            if (is_string($r) && str_starts_with($r, '/src/')) {
                return ltrim(self::sinPuntos($r), '/');
            }
        }
        $path = self::sinPuntos(rawurldecode((string) parse_url($requestUri, PHP_URL_PATH)));
        foreach (['/frontend/', '/src/'] as $raiz) {
            $pos = strpos($path, $raiz);
            if ($pos !== false) {
                return substr($path, $pos + 1);
            }
        }

        return basename($path);
    }

    /** Resuelve los segmentos `.` y `..` de una ruta, como hace el servidor web antes de servirla. */
    private static function sinPuntos(string $path): string
    {
        $segmentos = [];
        foreach (explode('/', str_replace('\\', '/', $path)) as $segmento) {
            if ($segmento === '..') {
                array_pop($segmentos);
            } elseif ($segmento !== '.') {
                $segmentos[] = $segmento;
            }
        }

        return implode('/', $segmentos);
    }
}
