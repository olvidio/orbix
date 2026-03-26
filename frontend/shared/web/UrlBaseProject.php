<?php

namespace frontend\shared\web;

class UrlBaseProject
{

    /**
     * Construye una URL completa combinando el host actual con una ruta específica.
     * @return string URL completa (ej: "http://orbix.docker:8003/orbix/src/usuarios/...")
     */
    public static function getUrlBase()
    {
        // 1. Detectar Protocolo (http o https)
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

        // 2. Obtener Host y Puerto (orbix.docker:8003)
        // Usamos HTTP_HOST porque es el que mantiene el puerto en Docker
        $host = $_SERVER['HTTP_HOST'];

        // 3. Obtener la base de la URI (el primer segmento, ej: /orbix/)
        // Limpiamos la URI actual para evitar parámetros GET
        $uri_limpia = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segmentos = explode('/', trim($uri_limpia, '/'));

        // Cogemos el primer segmento (normalmente el nombre de la carpeta del proyecto)
        $base_proyecto = !empty($segmentos[0]) ? '/' . $segmentos[0] : '';

        // 4. Limpiar el path de destino para que no tenga slashes duplicados
        //$path_destino = ltrim($path_destino, '/');

        // 5. Montar la URL final
        //return $protocolo . "://" . $host . $base_proyecto . '/' . $path_destino;
        return $protocolo . "://" . $host . $base_proyecto . '/';
    }
}