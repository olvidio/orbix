<?php

namespace frontend\shared\web;

class UrlBaseProject
{

    /**
     * Construye una URL completa combinando el host actual con una ruta específica.
     * @return string URL completa (ej: "http://orbix.docker:8003/orbix/src/usuarios/...")
     */
    public static function getUrlBase(): string
    {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        $host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST'])
            ? $_SERVER['HTTP_HOST']
            : 'localhost';

        $requestUri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])
            ? $_SERVER['REQUEST_URI']
            : '/';
        $uri_limpia = parse_url($requestUri, PHP_URL_PATH);
        $path = is_string($uri_limpia) ? $uri_limpia : '/';
        $segmentos = explode('/', trim($path, '/'));

        $base_proyecto = $segmentos[0] !== '' ? '/' . $segmentos[0] : '';

        return $protocolo . '://' . $host . $base_proyecto . '/';
    }
}
