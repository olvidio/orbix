<?php

namespace frontend\shared;

use src\shared\config\ConfigGlobal;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use frontend\shared\security\HashFront;

class PostRequest
{

    /**
     * Para enviar archivos pdf (...) en los parámetros:
     * @param array|string $url
     * @param array $hash_params
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getDataMultipart(array|string $url, array $hash_params): mixed
    {
        $url = preg_replace('/(.*?)\.docker/', 'host.docker.internal', $url);

        // Store the cookies from the response in the cookie jar
        $cookieJar = new CookieJar();
        $cookies = $_COOKIE;
        foreach ($cookies as $name => $value) {
            $setCookie = new SetCookie(['name' => $name, 'value' => $value]);
            $cookieJar->setCookie($setCookie);
        }

        //$domain = 'docker.internal';
        $domain = strtolower(parse_url($url, PHP_URL_HOST));
        $jar = CookieJar::fromArray($cookies, $domain);

        // Use a specific cookie jar
        $client = new Client();
        $response2 = $client->request('POST', $url, [
            'cookies' => $jar,
            'multipart' => $hash_params
        ]);

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
            exit ($rta_json['mensaje']);
        }

        return json_decode($rta_json['data'], true);
    }

    /**
     * Parámetros de la petición HTTP actual para repetir el hash en llamadas server-to-server.
     * Debe coincidir con validatePost en global_header_front.inc / global_object.inc:
     * POST si el cuerpo no está vacío; si no, GET cuando existe el parámetro `h` (p. ej. HashFront::link).
     */
    public static function requestPayloadForHash(): array
    {
        return (!empty($_POST)) ? $_POST : ((isset($_GET['h'])) ? $_GET : []);
    }

    public static function getDataFromUrl(string $url, array $campos = []): mixed
    {
        // Compatibilidad: aceptar URL absoluta o relativa.
        if (!preg_match('#^https?://#i', $url)) {
            $url = rtrim(ConfigGlobal::getWeb(), '/') . '/' . ltrim($url, '/');
        }
        $url_hased = HashFront::cmdSinParametros($url);

        $oHash = new HashFront();
        $oHash->setUrl($url_hased);
        if (!empty($campos)) {
            $campos = self::normalizeCamposParaHash($campos);
            $oHash->setArrayCamposHidden($campos);
        }
        $hash_params = $oHash->getArrayCampos();

        $data = self::getData($url_hased, $hash_params);
        if (!empty($data['error'])) {
            exit ($data['error']);
        }
        return $data;
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
     * @param array|string $url
     * @param array $hash_params
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function getData(array|string $url, array $hash_params): mixed
    {
        // Store the cookies from the response in the cookie jar
        $cookieJar = new CookieJar();
        $cookies = $_COOKIE;
        foreach ($cookies as $name => $value) {
            $setCookie = new SetCookie(['name' => $name, 'value' => $value]);
            $cookieJar->setCookie($setCookie);
        }

        $parts = parse_url($url);
        // 1. Canviem el host original (ex: orbix.docker) per l'intern de Docker
        $host_original = $parts['host'] ?? '';
        $host_nuevo = preg_replace('/(.*?)\.docker/', 'host.docker.internal', $host_original);

        // 2. Path: Limpiamos la solamente la 3a posición si tiene guión
        $path_final = $parts['path'] ?? '';

        if (!empty($path_final)) {
            // Convertim el path en array. Exemple: "/orbix/H-dlbv/src/..."
            // es converteix en ['', 'orbix', 'H-dlbv', 'src', ...]
            $segments = explode('/', $path_final);

            // Verifiquem si existeix la posició 2 (la tercera peça després de la primera /)
            // i si realment conté un guionet '-'
            if (isset($segments[2]) && strpos($segments[2], '-') !== false) {
                // Eliminem el segment amb guionet de l'array
                unset($segments[2]);
                // Tornem a muntar la ruta i reindexem
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
        $domain_org = parse_url($url_limpia, PHP_URL_HOST);
        $domain = strtolower($domain_org);
        $jar = CookieJar::fromArray($cookies, $domain);

        // Use a specific cookie jar
        $client = new Client();
        $response2 = $client->request('POST', $url_limpia, [
            'cookies' => $jar,
            'form_params' => $hash_params
        ]);

        $code = $response2->getStatusCode(); // 200
        $reason = $response2->getReasonPhrase(); // OK
        $body = $response2->getBody();
        $content = $body->getContents();
        $rta_json = json_decode($content, TRUE); //remainingBytes
        if (json_last_error() !== JSON_ERROR_NONE) {
            if (is_string($content)) {
                $msg = sprintf(_("Respuesta de: %s"), $url);
                $msg .= "<br>" . $content;
                return ['error' => $msg];
            }
        }

        if ($rta_json === null) {
            $msg = sprintf(_("No se obtiene respuesta de: %s"), $url);
            return ['error' => $msg];
        }
        if (!$rta_json['success']) {
            return ['error' => $rta_json['mensaje']];
        }

        return json_decode($rta_json['data'], true);
    }

    public static function getContent(array|string $url, array $hash_params): mixed
    {
        //$url2 = str_replace('orbix.docker', 'host.docker.internal', $url);
        $url = preg_replace('/(.*?)\.docker/', 'host.docker.internal', $url);

        // Store the cookies from the response in the cookie jar
        $cookieJar = new CookieJar();
        $cookies = $_COOKIE;
        foreach ($cookies as $name => $value) {
            $setCookie = new SetCookie(['name' => $name, 'value' => $value]);
            $cookieJar->setCookie($setCookie);
        }

        //$domain = 'docker.internal';
        $domain = strtolower(parse_url($url, PHP_URL_HOST));
        $jar = CookieJar::fromArray($cookies, $domain);

        // Use a specific cookie jar
        $client = new Client();
        $response2 = $client->request('POST', $url, [
            'cookies' => $jar,
            'form_params' => $hash_params
        ]);

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
        $url = preg_match('#^https?://#i', $relativeUrl)
            ? $relativeUrl
            : rtrim(ConfigGlobal::getWeb(), '/') . '/' . ltrim($relativeUrl, '/');

        $cookies = $_COOKIE;
        $parts = parse_url($url);
        $host_original = $parts['host'] ?? '';
        $host_nuevo = preg_replace('/(.*?)\.docker/', 'host.docker.internal', $host_original);

        $path_final = $parts['path'] ?? '';
        if ($path_final !== '') {
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

        $domain_org = parse_url($url_limpia, PHP_URL_HOST);
        $domain = strtolower((string)$domain_org);
        $jar = CookieJar::fromArray($cookies, $domain);

        $client = new Client();
        $response = $client->request('POST', $url_limpia, [
            'cookies' => $jar,
            'form_params' => $formParams,
        ]);

        return (string)$response->getBody()->getContents();
    }
}
