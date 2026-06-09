<?php

namespace frontend\shared;

use frontend\shared\config\OrbixRuntime;
use GuzzleHttp\Client;
use frontend\shared\security\HashFront;

class PostRequest
{
    /**
     * Clave reservada cuando el JSON interno de `data` es un escalar (poco frecuente).
     * Los endpoints habituales devuelven objeto/array; los ack tipo `'ok'` se mapean a `[]`.
     *
     * @see envelopeDataFieldToArray()
     */
    private const INNER_SCALAR_ENVELOPE_KEY = '__postRequestScalar';

    /**
     * Convierte el campo `data` del envelope JSON de {@see \src\shared\web\ContestarJson::enviar}
     * en un array asociativo.
     *
     * Comportamiento respecto al antiguo {@see json_decode}(..., true) directo:
     *
     * | Origen típico | Antes | Ahora |
     * |---------------|-------|-------|
     * | `data` = string JSON de objeto/array (caso normal) | `array` | igual |
     * | `data` = `'ok'`, `'none'`, texto no-JSON, `''` | `null` | `[]` |
     * | literal JSON `null` | `null` | `[]` |
     * | JSON escalar `"x"` o `42` | string/int/… | `['__postRequestScalar' => …]` |
     *
     * @return array<int|string, mixed>
     */
    private static function envelopeDataFieldToArray(mixed $raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if ($raw === null) {
            return [];
        }
        if (!is_string($raw)) {
            return [self::INNER_SCALAR_ENVELOPE_KEY => $raw];
        }
        $trim = trim($raw);
        if ($trim === '') {
            return [];
        }
        $decoded = json_decode($trim, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        if ($decoded === null) {
            return [];
        }
        if (is_array($decoded)) {
            return $decoded;
        }

        return [self::INNER_SCALAR_ENVELOPE_KEY => $decoded];
    }

    /**
     * Para enviar archivos pdf (...) en los parámetros:
     * @param string $url
     * @param array<string, mixed> $hash_params
     * @return array<int|string, mixed>
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getDataMultipart(string $url, array $hash_params): array
    {
        $url = self::absoluteHttpUrlFromAppRelative($url);
        $parts = parse_url($url);
        $host_original = $parts['host'] ?? '';
        $url = preg_replace('/(.*?)\.docker/', 'host.docker.internal', $url);
        $host_rewritten = (string) parse_url($url, PHP_URL_HOST);

        $cookies = self::cookiesForInternalRequest();

        // Use a specific cookie jar
        $client = new Client();
        $reqOpts = self::withPreservedHttpHostHeader(
            $host_original,
            $host_rewritten,
            $parts,
            array_merge(
                self::internalGuzzleOptions($cookies, ['multipart' => $hash_params]),
            )
        );
        $response2 = $client->request('POST', $url, $reqOpts);

        $code = $response2->getStatusCode(); // 200
        $reason = $response2->getReasonPhrase(); // OK
        $body = $response2->getBody();
        $content = $body->getContents();
        if (is_string($content)) {
            $msg = sprintf(_("Respuesta de: %s"), $url);
            $msg .= "<br>" . $content;
            return ['error' => $msg];
        }
        $rta_json = json_decode($body->getContents(), TRUE); //remainingBytes

        if ($rta_json === null) {
            $msg = sprintf(_("No se obtiene respuesta de: %s"), $url);
            exit ($msg);
        }
        if (!$rta_json['success']) {
            exit((string) $rta_json['mensaje'] . self::sufijoDiagnosticoLlamadaInterna($url));
        }

        return self::envelopeDataFieldToArray($rta_json['data'] ?? null);
    }

    /**
     * POST interno (URL ya resuelta/hasheada si aplica). Misma respuesta que {@see getDataFromUrl}
     * sin envolver en HashFront desde ruta relativa.
     *
     * @param string $url
     * @param array<string, mixed> $hash_params
     * @return array<int|string, mixed>
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getData(string $url, array $hash_params): array
    {
        return self::getDataInternal($url, $hash_params);
    }

    /**
     * Parámetros de la petición HTTP actual para repetir el hash en llamadas server-to-server.
     * Debe coincidir con validatePost en global_header_front.inc y
     * frontend/shared/bootstrap/after_global_object.inc (tras global_object.inc):
     * POST si el cuerpo no está vacío; si no, GET cuando existe el parámetro `h` (p. ej. HashFront::link).
     */
    public static function requestPayloadForHash(): array
    {
        return (!empty($_POST)) ? $_POST : ((isset($_GET['h'])) ? $_GET : []);
    }

    /**
     * URL absoluta para peticiones internas. Las rutas `/src/...` deben ir en el path público
     * tal como las ve el navegador (`…/orbix/…/src/…`), sin segmento `/public/`: el front controller
     * es `public/index.php`, pero FastRoute recibe `/src/…` tras quitar prefijos; si la URL lleva
     * `/public/src/` el despacho acaba en 404.
     */
    private static function absoluteHttpUrlFromAppRelative(string $url): string
    {
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        return rtrim(OrbixRuntime::getWeb(), '/') . '/' . ltrim($url, '/');
    }

    /**
     * POST interno firmado (HashFront) al backend; devuelve siempre un array.
     *
     * - Si el backend responde error en envelope: `['error' => '…html…']` y, por defecto, `exit`.
     * - Si éxito: el contenido útil es {@see envelopeDataFieldToArray} sobre `data`
     *   (nunca `null`; ack `'ok'` → `[]`). Ver tabla en {@see envelopeDataFieldToArray}.
     *
     * @param array<string, mixed> $campos
     * @param bool $exitOnError Si es false, devuelve `['error' => '…']` en lugar de hacer `exit`.
     * @return array<int|string, mixed>
     */
    public static function getDataFromUrl(string $url, array $campos = [], bool $exitOnError = true): array
    {
        $url = self::absoluteHttpUrlFromAppRelative($url);
        $url_hased = HashFront::cmdSinParametros($url);

        // Si el payload proviene de $_POST (PostRequest::requestPayloadForHash),
        // arrastra los meta-campos de hash del navegador (h/hh/hhc/hno/horig/…).
        // Esta función genera su propio hash para la llamada server-to-server;
        // `HashFront::getArrayCampos()` hace `array_merge(paramHash, camposHidden)`
        // y los meta-campos del navegador sobrescribirían el hash fresco, causando
        // que `validatePost` en el endpoint rechace el POST y redirija a index.php.
        $campos = self::stripInboundHashMeta($campos);

        $oHash = new HashFront();
        $oHash->setUrl($url_hased);
        if (!empty($campos)) {
            $campos = self::normalizeCamposParaHash($campos);
            $oHash->setArrayCamposHidden($campos);
        }
        $hash_params = $oHash->getArrayCampos();

        $data = self::getData($url_hased, $hash_params);
        if (!empty($data['error'])) {
            if ($exitOnError) {
                exit($data['error']);
            }
            return $data;
        }
        return $data;
    }

    /**
     * Marca el inicio del bloque técnico (URL, página, procedencia) en errores de {@see getDataInternal}.
     */
    private const DIAGNOSTIC_MARKER = '<!-- postRequestDiagnostic -->';

    /**
     * Quita el sufijo técnico de {@see sufijoDiagnosticoLlamadaInterna} para mostrar el aviso al usuario.
     */
    public static function stripInternalCallProvenance(string $errorHtml): string
    {
        $pos = strpos($errorHtml, self::DIAGNOSTIC_MARKER);
        if ($pos !== false) {
            return substr($errorHtml, 0, $pos);
        }

        $needle = '<br><strong>' . _('Procedencia') . ':';
        $pos = strpos($errorHtml, $needle);
        if ($pos === false) {
            return $errorHtml;
        }

        return substr($errorHtml, 0, $pos);
    }

    /**
     * Quita los meta-campos de hash (h, hh, hhc, horig, hhorig, hno, hchk, hnov)
     * y los `scroll_id_*` que `validatePost`/`ordenarArrayParam` eliminan al
     * validar. Dejarlos en el payload server-to-server rompe la firma porque se
     * reintroducen como camposHidden y `array_merge` les da preferencia sobre
     * el hash fresco calculado aquí.
     *
     * También quita `PHPSESSID`, `atras` y `hpos`:
     * - `PHPSESSID`: `fnjs_update_div` añade `&PHPSESSID=1`; si formara parte del hash hidden,
     *   el receptor lo borra antes de recalcular `hh` → firma rota → 302.
     * - `atras`: análogo (campo auxiliar del POST que no debe mezclarse con la firma nueva).
     * - `hpos`: `web\Posicion` / `HashFront::add_hash` ponen `hpos=1` al volver atrás; entonces
     *   `validatePost` recalcula `h` con `realFullUrl()` + query (flujo Posición). La llamada
     *   server-to-server a otro script (p.ej. `dossiers_ver_pantalla_data`) firma `h` con
     *   `ordenarQuery(camposForm)` (flujo formulario). Si se reenvía `hpos`, receptor y emisor
     *   usan reglas distintas → 302 a index.php.
     *
     * @param array<string, mixed> $campos
     * @return array<string, mixed>
     */
    private static function stripInboundHashMeta(array $campos): array
    {
        foreach (['h', 'hh', 'hhc', 'horig', 'hhorig', 'hno', 'hchk', 'hnov', 'hc', 'PHPSESSID', 'atras', 'hpos'] as $metaKey) {
            unset($campos[$metaKey]);
        }
        foreach (array_keys($campos) as $k) {
            if (is_string($k) && str_starts_with($k, 'scroll_id_')) {
                unset($campos[$k]);
            }
        }

        return $campos;
    }

    /**
     * Un array vacío no genera inputs hidden en web\HashFront::getCamposHiddenHtml;
     * validatePost trata el campo ausente como ''.
     * Firmar con [] hace que http_build_query difiera de '' y falle el hash hh.
     *
     * No sustituir [] por '': un escalar vacío en POST + FILTER_REQUIRE_ARRAY
     * hace que filter_input devuelva false y (array)false sea [false] en el backend.
     * Omitir la clave equivale a "sin fases" y filter_input(null) → (array) → [].
     */
    private static function normalizeCamposParaHash(array $campos): array
    {
        foreach ($campos as $k => $v) {
            if (is_array($v) && $v === []) {
                unset($campos[$k]);
            }
        }
        return $campos;
    }

    /**
     * Al sustituir *.docker por host.docker.internal, la conexión TCP llega al vhost correcto
     * pero el header Host sería el interno; PHP y la app usan HTTP_HOST del navegador
     * (sesión, esquema, validaciones). Forzar el Host público evita “perder el hilo”.
     *
     * @param array<string, mixed> $parsedOriginal resultado de parse_url() antes del rewrite
     * @param array<string, mixed> $requestOptions opciones Guzzle (se añade 'headers')
     * @return array<string, mixed>
     */
    private static function withPreservedHttpHostHeader(
        string $hostOriginal,
        string $hostRewritten,
        array $parsedOriginal,
        array $requestOptions
    ): array {
        if ($hostRewritten === $hostOriginal || $hostOriginal === '') {
            return $requestOptions;
        }
        if (!empty($_SERVER['HTTP_HOST'])) {
            $hostHeader = (string) $_SERVER['HTTP_HOST'];
        } else {
            $hostHeader = $hostOriginal;
            if (!empty($parsedOriginal['port'])) {
                $hostHeader .= ':' . $parsedOriginal['port'];
            }
        }
        $requestOptions['headers'] = array_merge($requestOptions['headers'] ?? [], ['Host' => $hostHeader]);

        return $requestOptions;
    }

    /**
     * Cookies para POST internos al backend (/src/...).
     *
     * En el mismo request del login, el navegador aún no ha enviado PHPSESSID en
     * $_COOKIE aunque session_start() ya creó sesión autenticada; sin session_id()
     * la llamada Guzzle recibe HTML del formulario de login en lugar de JSON.
     *
     * @return array<string, string>
     */
    private static function cookiesForInternalRequest(): array
    {
        $cookies = [];
        foreach ($_COOKIE as $name => $value) {
            if (is_string($name) && (is_string($value) || is_numeric($value))) {
                $cookies[$name] = (string) $value;
            }
        }
        $sessionName = session_name();
        if ($sessionName === '') {
            $sessionName = 'PHPSESSID';
        }
        $sid = session_id();
        if ($sid === '' && isset($_POST[$sessionName]) && is_string($_POST[$sessionName])
            && preg_match('/^[a-zA-Z0-9,-]{16,128}$/', $_POST[$sessionName])) {
            $sid = $_POST[$sessionName];
        }
        if ($sid !== '') {
            $cookies[$sessionName] = $sid;
        }

        return $cookies;
    }

    /**
     * Cabecera Cookie explícita para Guzzle.
     *
     * No usamos CookieJar: al reescribir *.docker → host.docker.internal y forzar
     * Host público (orbix.docker), el jar no envía cookies por dominio distinto.
     *
     * @param array<string, string> $cookies
     */
    private static function buildCookieHeader(array $cookies): string
    {
        $parts = [];
        foreach ($cookies as $name => $value) {
            $parts[] = $name . '=' . $value;
        }

        return implode('; ', $parts);
    }

    /**
     * @param array<string, string> $cookies
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private static function internalGuzzleOptions(array $cookies, array $extra = []): array
    {
        $options = $extra;
        if ($cookies !== []) {
            $options['headers'] = array_merge($options['headers'] ?? [], [
                'Cookie' => self::buildCookieHeader($cookies),
            ]);
        }

        return $options;
    }

    private static function looksLikeLoginHtml(string $content): bool
    {
        return preg_match('/id=["\']frm_login["\']|form-signin|class=["\']login["\']/i', $content) === 1;
    }

    /**
     * @param string $url
     * @param array<string, mixed> $hash_params
     * @return array<int|string, mixed>
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function getDataInternal(string $url, array $hash_params): array
    {
        $cookies = self::cookiesForInternalRequest();

        $parts = parse_url($url);
        // 1. Canviem el host original (ex: orbix.docker) per l'intern de Docker
        $host_original = $parts['host'] ?? '';
        $host_nuevo = preg_replace('/(.*?)\.docker/', 'host.docker.internal', $host_original);

        // 2. Path: solo amb host reescrit a Docker ( *.docker → host.docker.internal ), treiem
        // el segment d'esquema amb guió a la posició 2 (ex. "/orbix/H-dlbv/src/...").
        // En instal·lacions HTTP reals, el mateix gest pot treure '/pruebas/H-XXX' del URL
        // intern i el vhost espera '/pruebas/H-XXX/src/...' → 404.
        $path_final = $parts['path'] ?? '';

        $dockerHostRewrite = ($host_nuevo !== $host_original && $host_original !== '');
        if ($dockerHostRewrite && $path_final !== '') {
            $segments = explode('/', $path_final);
            if (isset($segments[2]) && strpos($segments[2], '-') !== false) {
                unset($segments[2]);
                $path_final = implode('/', $segments);
            }
        }

        // 2. Reconstruïm incloent el protocol (scheme)
        $url_limpia = "";
        if (isset($parts['scheme'])) {
            $url_limpia .= $parts['scheme'] . "://"; // Aquí afegim el http:// o https://
        }
        $url_limpia .= $host_nuevo;
        if (isset($parts['port'])) {
            $url_limpia .= ':' . $parts['port'];
        }
        $url_limpia .= $path_final;

        if (isset($parts['query'])) {
            $url_limpia .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $url_limpia .= '#' . $parts['fragment'];
        }

        //$domain = 'docker.internal';
        $client = new Client();
        $reqOpts = self::withPreservedHttpHostHeader(
            $host_original,
            $host_nuevo,
            $parts,
            array_merge(
                self::internalGuzzleOptions($cookies, [
                    'form_params' => $hash_params,
                    'allow_redirects' => false,
                    'http_errors' => false,
                ]),
            )
        );
        $response2 = $client->request('POST', $url_limpia, $reqOpts);

        $code = $response2->getStatusCode();
        if ($code >= 300 && $code < 400) {
            $location = $response2->getHeaderLine('Location');
            $msg = sprintf(
                _("Redirección inesperada en llamada interna a %s (status %d → %s)"),
                $url_limpia,
                $code,
                $location !== '' ? $location : _('sin Location')
            );
            $msg .= '<br>' . _("Probable causa: validación de hash/sesión. Revisa que el endpoint no requiera firma que no se está generando igual server-to-server.");
            return ['error' => $msg . self::sufijoDiagnosticoLlamadaInterna($url_limpia)];
        }
        if ($code >= 400) {
            $msg = sprintf(_("Error HTTP %d en llamada interna a %s"), $code, $url_limpia);
            return ['error' => $msg . self::sufijoDiagnosticoLlamadaInterna($url_limpia)];
        }
        $body = $response2->getBody();
        $content = $body->getContents();
        $rta_json = json_decode($content, TRUE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            if (self::looksLikeLoginHtml((string) $content)) {
                $msg = sprintf(
                    _('Sesión no autenticada en llamada interna a %s (status %d).'),
                    $url_limpia,
                    $code
                );
                $msg .= '<br>' . _('El backend ha devuelto el formulario de login en lugar de JSON. Vuelve a entrar en Orbix.');
                return ['error' => $msg . self::sufijoDiagnosticoLlamadaInterna($url_limpia)];
            }
            $preview = mb_substr(trim((string)$content), 0, 500);
            $msg = sprintf(_("Respuesta no-JSON de %s (status %d)."), $url_limpia, $code);
            $msg .= '<br>' . htmlspecialchars($preview, ENT_QUOTES, 'UTF-8');
            return ['error' => $msg . self::sufijoDiagnosticoLlamadaInterna($url_limpia)];
        }

        if ($rta_json === null) {
            $msg = sprintf(_("No se obtiene respuesta de: %s"), $url_limpia);
            return ['error' => $msg . self::sufijoDiagnosticoLlamadaInterna($url_limpia)];
        }
        if (!$rta_json['success']) {
            return ['error' => (string) $rta_json['mensaje'] . self::sufijoDiagnosticoLlamadaInterna($url_limpia)];
        }

        return self::envelopeDataFieldToArray($rta_json['data'] ?? null);
    }

    public static function getContent(array|string $url, array $hash_params): mixed
    {
        $url = self::absoluteHttpUrlFromAppRelative((string) $url);
        $parts = parse_url($url);
        $host_original = $parts['host'] ?? '';
        $url = preg_replace('/(.*?)\.docker/', 'host.docker.internal', (string) $url);
        $host_rewritten = (string) parse_url($url, PHP_URL_HOST);

        $cookies = self::cookiesForInternalRequest();

        // Use a specific cookie jar
        $client = new Client();
        $reqOpts = self::withPreservedHttpHostHeader(
            $host_original,
            $host_rewritten,
            $parts,
            array_merge(
                self::internalGuzzleOptions($cookies, ['form_params' => $hash_params]),
            )
        );
        $response2 = $client->request('POST', $url, $reqOpts);

        $code = $response2->getStatusCode(); // 200
        $reason = $response2->getReasonPhrase(); // OK

        return $response2->getBody()->getContents();
    }

    /**
     * POST interno con cookies de sesión (misma lógica de URL/host que {@see getData});
     * devuelve el cuerpo tal cual (p. ej. respuestas AJAX text/plain).
     */
    public static function sendRawPost(string $relativeUrl, array $formParams): string
    {
        $url = self::absoluteHttpUrlFromAppRelative($relativeUrl);

        $cookies = self::cookiesForInternalRequest();
        $parts = parse_url($url);
        $host_original = $parts['host'] ?? '';
        $host_nuevo = preg_replace('/(.*?)\.docker/', 'host.docker.internal', $host_original);

        $path_final = $parts['path'] ?? '';
        $dockerHostRewrite = ($host_nuevo !== $host_original && $host_original !== '');
        if ($dockerHostRewrite && $path_final !== '') {
            $segments = explode('/', $path_final);
            if (isset($segments[2]) && strpos($segments[2], '-') !== false) {
                unset($segments[2]);
                $path_final = implode('/', $segments);
            }
        }

        $url_limpia = '';
        if (isset($parts['scheme'])) {
            $url_limpia .= $parts['scheme'] . '://';
        }
        $url_limpia .= $host_nuevo;
        if (isset($parts['port'])) {
            $url_limpia .= ':' . $parts['port'];
        }
        $url_limpia .= $path_final;
        if (isset($parts['query'])) {
            $url_limpia .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $url_limpia .= '#' . $parts['fragment'];
        }

        $client = new Client();
        $reqOpts = self::withPreservedHttpHostHeader(
            $host_original,
            $host_nuevo,
            $parts,
            array_merge(
                self::internalGuzzleOptions($cookies, ['form_params' => $formParams]),
            )
        );
        $response = $client->request('POST', $url_limpia, $reqOpts);

        return (string)$response->getBody()->getContents();
    }

    /**
     * Contexto diagnóstico (endpoint, página del formulario, tipo de llamada) en errores de {@see getDataInternal}.
     */
    private static function sufijoDiagnosticoLlamadaInterna(string $urlLimpia): string
    {
        $s = self::DIAGNOSTIC_MARKER;
        $s .= '<br><strong>' . _('Endpoint') . ':</strong> <code>'
            . htmlspecialchars($urlLimpia, ENT_QUOTES, 'UTF-8') . '</code>';
        if (!empty($_SERVER['REQUEST_URI'])) {
            $s .= '<br><strong>' . _('Página') . ':</strong> <code>'
                . htmlspecialchars((string) $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') . '</code>';
        }
        $s .= self::procedenciaLlamadaInternaGetData();

        return $s;
    }

    /**
     * Sufijo para mensajes de error de {@see getData}: indica que el fallo procede del POST
     * interno (p.ej. `dossiers_ver.php` → `/src/dossiers/*_data`), no de una petición del navegador a esa URL.
     */
    private static function procedenciaLlamadaInternaGetData(): string
    {
        return '<br><strong>' . _('Procedencia') . ':</strong> '
            . '<code>' . htmlspecialchars(self::class . '::getData', ENT_QUOTES, 'UTF-8') . '</code>'
            . ' — ' . _('POST interno firmado (HashFront) con PHPSESSID (cookie o session_id()).');
    }
}
