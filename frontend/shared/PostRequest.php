<?php

namespace frontend\shared;

use core\ConfigGlobal;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use web\Hash;

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

    public static function getDataFromUrl(string $url, array $campos = []): mixed
    {
        // Compatibilidad: aceptar URL absoluta o relativa.
        if (!preg_match('#^https?://#i', $url)) {
            $url = rtrim(ConfigGlobal::getWeb(), '/') . '/' . ltrim($url, '/');
        }
        $url_hased = Hash::cmdSinParametros($url);

        $oHash = new Hash();
        $oHash->setUrl($url_hased);
        if (!empty($campos)) {
            $oHash->setArrayCamposHidden($campos);
        }
        $hash_params = $oHash->getArrayCampos();

        $data =  self::getData($url_hased, $hash_params);
        if (!empty($data['error'])) {
            exit ($data['error']);
        }
        return $data;
    }

    /**
     * @param array|string $url
     * @param array $hash_params
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getData(array|string $url, array $hash_params): mixed
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
        $host_nuevo = 'host.docker.internal';
        // 2. Reconstruïm la URL
        // Nota: No incloem el 'scheme' (http/https) ni el 'host' antic
        // per complir amb el teu requisit de que la part inicial "desaparegui"
        $url_limpia = $host_nuevo;
        if (isset($parts['port'])) {
            $url_limpia .= ':' . $parts['port'];
        }
        if (isset($parts['path'])) {
            $url_limpia .= $parts['path'];
        }
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
}
