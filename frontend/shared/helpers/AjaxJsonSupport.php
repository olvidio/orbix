<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

/**
 * Respuestas AJAX JSON ({success, mensaje?, data}) desde controladores frontend.
 *
 * @see \src\shared\web\ContestarJson
 * @see frontend/agents.md
 */
final class AjaxJsonSupport
{
    /**
     * @param string|array<int|string, mixed> $data
     */
    public static function response(string $error = '', string|array $data = 'ok'): never
    {
        \src\shared\web\ContestarJson::enviar($error, $data);
        exit;
    }

    public static function html(string $html, string $error = ''): never
    {
        // `data` anidado (una sola codificación JSON); el cliente usa `fnjs_extract_html_from_ajax_body`.
        \src\shared\web\ContestarJson::enviarDataAnidado($error, ['html' => $html]);
        exit;
    }

    /**
     * @param array<string, mixed> $vars
     */
    public static function renderPhtml(string $viewNamespace, string $template, array $vars = [], string $error = ''): never
    {
        $oView = new ViewNewPhtml($viewNamespace);
        ob_start();
        $oView->renderizar($template, $vars);
        self::html((string) ob_get_clean(), $error);
    }

    /**
     * Proxy POST → `/src/...` via PostRequest; en error hace exit (texto legacy).
     *
     * @param array<string, mixed> $campos
     */
    public static function proxyPostRequest(string $url, array $campos = []): never
    {
        PostRequest::getDataFromUrl($url, $campos);
        self::response();
    }

    /**
     * Convierte respuesta texto plano del backend (vacío = ok) a JSON.
     */
    public static function fromPlainText(string $text): never
    {
        $text = trim($text);
        if ($text !== '') {
            self::response($text);
        }
        self::response();
    }
}
