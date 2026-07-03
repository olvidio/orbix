<?php

namespace frontend\usuarios\controller;

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use frontend\shared\model\ViewNewPhtml;
use src\shared\application\HydratePermisosActividades;
use src\shared\web\ContestarJson;
use src\usuarios\application\LoginProcesar;
use frontend\shared\helpers\PayloadCoercion;


/**
 * Renderiza el formulario de login con los campos indicados.
 */
function render_login_form(
    string $username,
    string|false $ubicacion,
    string $idioma,
    string $esquema,
    int $error,
    string $esquema_web = ''
): void {
    $oDBPropiedades = new DBPropiedades();
    $a_campos = [
        'error' => $error,
        'ubicacion' => $ubicacion,
        'esquema_web' => $esquema_web,
        'DesplRegiones' => $oDBPropiedades->posibles_esquemas($esquema),
        'idioma' => $idioma,
        'username' => $username,
        'url' => AppUrlConfig::getPublicAppBaseUrl(),
    ];
    $oView = new ViewNewPhtml(__NAMESPACE__);
    $oView->renderizar('login_form.phtml', $a_campos);
}

/**
 * Petición a un endpoint JSON bajo `/src/...` (p. ej. PostRequest server-to-server).
 */
function orbix_es_peticion_api_src(): bool
{
    if (isset($_GET['r']) && is_string($_GET['r']) && str_starts_with($_GET['r'], '/src/')) {
        return true;
    }
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (!is_string($uri) || $uri === '') {
        return false;
    }
    $path = parse_url($uri, PHP_URL_PATH);

    return is_string($path) && str_contains($path, '/src/');
}

/**
 * Indica al cliente AJAX que debe recargar la aplicación (login completo).
 */
function orbix_marcar_respuesta_ajax_sin_sesion(): void
{
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null;
    $esAjax = is_string($requestedWith) && $requestedWith !== ''
        && strtolower($requestedWith) === 'xmlhttprequest';
    if ($esAjax || orbix_es_peticion_api_src()) {
        header('X-Orbix-Auth-Required: 1');
    }
}

/**
 * Respuesta JSON para endpoints `/src/...` cuando no hay sesión autenticada.
 */
function orbix_responder_login_api_sin_sesion(): void
{
    header('Content-Type: application/json; charset=UTF-8');
    orbix_marcar_respuesta_ajax_sin_sesion();
    ContestarJson::enviar(_('Sesión no autenticada'), ['code' => 'auth_required']);
}

$esquema_web = getenv('ESQUEMA');
$ubicacion = getenv('UBICACION');
$private = getenv('PRIVATE');

$_SESSION['sfsv'] = $ubicacion;

$esquema_web_str = is_string($esquema_web) ? $esquema_web : '';
$ubicacion_str = is_string($ubicacion) ? $ubicacion : '';
$private_str = is_string($private) ? $private : '';

if ($esquema_web_str !== '') {
    $oDBPropiedades = new DBPropiedades();
    $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(false, true);
    if (!is_array($a_posibles_esquemas) || !in_array($esquema_web_str, $a_posibles_esquemas, true)) {
        $msg = sprintf(_('No existe este equema: %s'), $esquema_web_str);
        die($msg);
    }
}

if (!isset($_SESSION['session_auth'])) {
    orbix_marcar_respuesta_ajax_sin_sesion();
    $idioma = '';

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $_SESSION['private'] = $private_str;

        $loginInput = UsuariosPostInput::loginInputFromPost();
        $useCase = new LoginProcesar();
        $result = $useCase->execute(
            $loginInput,
            $esquema_web_str,
            $ubicacion_str
        );

        if (!$result['ok'] && isset($result['redirect_ayuda_2fa'])) {
            $url_base = AppUrlConfig::getPublicAppBaseUrl() . '/';
            $a_params = $result['redirect_ayuda_2fa'];
            $a_params['url_base'] = $url_base;
            $url_ayuda = $url_base . 'frontend/usuarios/controller/ayuda_2fa_reset.php?'
                . http_build_query($a_params);
            header("Location: $url_ayuda");
            die();
        }

        if (!$result['ok']) {
            if (orbix_es_peticion_api_src()) {
                orbix_responder_login_api_sin_sesion();
            }
            $error = \frontend\shared\helpers\PayloadCoercion::int($result['error'] ?? 1);
            $esquema_form = $loginInput['esquema'] !== '' ? $loginInput['esquema'] : $esquema_web_str;
            render_login_form($loginInput['username'], $ubicacion, $idioma, $esquema_form, $error, $esquema_web_str);
            die();
        }

        $loginSession = UsuariosPayload::loginOkSessionFromResult($result);
        if ($loginSession === null) {
            if (orbix_es_peticion_api_src()) {
                orbix_responder_login_api_sin_sesion();
            }
            render_login_form($loginInput['username'], $ubicacion, $idioma, $loginInput['esquema'], 1, $esquema_web_str);
            die();
        }

        HydratePermisosActividades::invalidateSessionCache();
        $_SESSION['session_auth'] = $loginSession['session_auth'];
        $_SESSION['config'] = $loginSession['session_config'];

        UsuariosPayload::cambiarIdioma();

        $time_expire_cookie = time() + (86400 * 30);
        $arr_cookie_options = [
            'expires' => $time_expire_cookie,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        setcookie('esquema', $loginSession['esquema'], $arr_cookie_options);
        setcookie('idioma', $loginSession['idioma'], $arr_cookie_options);
    } else {
        if (orbix_es_peticion_api_src()) {
            orbix_responder_login_api_sin_sesion();
        }
        $esquema = \frontend\shared\helpers\PayloadCoercion::string($_COOKIE['esquema'] ?? '');
        $idioma = \frontend\shared\helpers\PayloadCoercion::string($_COOKIE['idioma'] ?? '');
        UsuariosPayload::cambiarIdioma($idioma);
        render_login_form('', $ubicacion, $idioma, $esquema, 0, $esquema_web_str);
        die();
    }
} else {
    UsuariosPayload::cambiarIdioma();
}

if (!isset($_SESSION['session_go_to'])) {
    $_SESSION['session_go_to'] = 'a';
    $primera = 1;
} elseif (!empty($_SESSION['Refresh_continue_primera'])) {
    $primera = 1;
    unset($_SESSION['Refresh_continue_primera']);
}
