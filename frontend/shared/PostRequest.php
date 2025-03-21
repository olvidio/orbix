<?php

namespace frontend\shared;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class PostRequest
{

    /**
     * @param array|string $url
     * @param array $hash_params
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getData(array|string $url, array $hash_params): mixed
    {
        $url = str_replace('orbix.docker', 'host.docker.internal', $url);

        // Store the cookies from the response in the cookie jar
        $cookieJar = new CookieJar();
        $cookies = $_COOKIE;
        foreach ($cookies as $name => $value) {
            $setCookie = new SetCookie(['name' => $name, 'value' => $value]);
            $cookieJar->setCookie($setCookie);
        }

        //$domain = 'docker.internal';
        $domain = strtolower( parse_url( $url , PHP_URL_HOST ) );
        $jar = CookieJar::fromArray($cookies, $domain);

        // Use a specific cookie jar
        $client = new Client();
        $response2 = $client->request('POST', $url, [
            'cookies' => $jar,
            'form_params' => $hash_params
        ]);

        $code = $response2->getStatusCode(); // 200
        $reason = $response2->getReasonPhrase(); // OK
        $body = $response2->getBody();
        $rta_json = json_decode($body->getContents(), TRUE, ); //remainingBytes

        if (!$rta_json['success']) {
            exit ($rta_json['mensaje']);
        }

        return json_decode($rta_json['data'], true);
    }

    public static function getContent(array|string $url, array $hash_params): mixed
    {
        $url = str_replace('orbix.docker', 'host.docker.internal', $url);

        // Store the cookies from the response in the cookie jar
        $cookieJar = new CookieJar();
        $cookies = $_COOKIE;
        foreach ($cookies as $name => $value) {
            $setCookie = new SetCookie(['name' => $name, 'value' => $value]);
            $cookieJar->setCookie($setCookie);
        }

        //$domain = 'docker.internal';
        $domain = strtolower( parse_url( $url , PHP_URL_HOST ) );
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