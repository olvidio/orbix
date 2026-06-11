<?php

/**
 * Helpers para respuestas AJAX JSON ({success, mensaje?, data}) desde controladores frontend.
 *
 * @see \src\shared\web\ContestarJson
 * @see frontend/agents.md
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use src\shared\web\ContestarJson;

/**
 * @param string|array<int|string, mixed> $data
 */
function ajax_json_response(string $error = '', string|array $data = 'ok'): never
{
    ContestarJson::enviar($error, $data);
    exit;
}

function ajax_json_html(string $html, string $error = ''): never
{
    ContestarJson::enviar($error, ['html' => $html]);
    exit;
}

/**
 * @param array<string, mixed> $vars
 */
function ajax_json_render_phtml(string $viewNamespace, string $template, array $vars = [], string $error = ''): never
{
    $oView = new ViewNewPhtml($viewNamespace);
    ob_start();
    $oView->renderizar($template, $vars);
    ajax_json_html((string) ob_get_clean(), $error);
}

/**
 * Proxy POST → `/src/...` via PostRequest; en error hace exit (texto legacy).
 *
 * @param array<string, mixed> $campos
 */
function ajax_json_proxy_post_request(string $url, array $campos = []): never
{
    PostRequest::getDataFromUrl($url, $campos);
    ajax_json_response();
}

/**
 * Convierte respuesta texto plano del backend (vacío = ok) a JSON.
 */
function ajax_json_from_plain_text(string $text): never
{
    $text = trim($text);
    if ($text !== '') {
        ajax_json_response($text);
    }
    ajax_json_response();
}
