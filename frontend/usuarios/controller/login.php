<?php

namespace frontend\usuarios\controller;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\application\LoginProcesar;

/**
 * Guardia de sesion del sistema web.
 *
 * No es una URL convencional: se incluye via `require_once(...)` desde los
 * bootstraps globales (`apps/core/global_object.inc` y
 * `frontend/shared/global_header_front.inc`) en CADA request.
 *
 *   - Si hay `$_SESSION['session_auth']` ya asentada -> asegura gettext y
 *     sigue.
 *   - Si no hay sesion y llega POST con username/password -> invoca
 *     `src\usuarios\application\LoginProcesar` para validar y, si todo va
 *     bien, rellena `$_SESSION['session_auth']` y `$_SESSION['config']`,
 *     pone cookies y deja que la request continue.
 *   - Si la validacion da error o no hay POST -> dibuja el form de login y
 *     mata la request.
 *
 * Se asume que el autoload de Composer ya esta cargado y `session_start()`
 * ya ha sido invocado por quien incluye este fichero.
 */

/**
 * Ajusta locale y gettext al idioma preferido (sesion, navegador o default).
 */
function cambiar_idioma($idioma = '')
{
    if (empty($idioma)) {
        if (empty($_SESSION['session_auth']['idioma'])) {
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $a_idiomas = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                $numero_de_idiomas = count($a_idiomas);
                for ($i = 0; $i < $numero_de_idiomas; $i++) {
                    if (!isset($idioma)) {
                        if (substr($a_idiomas[$i], 0, 2) === 'ca') {
                            $idioma = 'ca_ES.UTF-8';
                        }
                        if (substr($a_idiomas[$i], 0, 2) === 'es') {
                            $idioma = 'es_ES.UTF-8';
                        }
                        if (substr($a_idiomas[$i], 0, 2) === 'en') {
                            $idioma = 'en_US.UTF-8';
                        }
                        if (substr($a_idiomas[$i], 0, 2) === 'de') {
                            $idioma = 'de_DE.UTF-8';
                        }
                    }
                }
            }
        } else {
            $idioma = $_SESSION['session_auth']['idioma'];
        }
        if (!isset($idioma)) {
            $idioma = $_SESSION['oConfig']->getIdioma_default();
        }
    }
    $domain = 'orbix';
    setlocale(LC_ALL, '');
    putenv("LC_ALL=''");
    putenv('LANGUAGE=');

    setlocale(LC_ALL, $idioma);
    putenv("LC_ALL={$idioma}");
    putenv("LANG={$idioma}");

    bindtextdomain($domain, OrbixRuntime::gettextLanguagesDir());
    textdomain($domain);
    bind_textdomain_codeset($domain, 'UTF-8');
}

/**
 * Indica al cliente AJAX que debe recargar la aplicación (login completo).
 */
function orbix_marcar_respuesta_ajax_sin_sesion(): void
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('X-Orbix-Auth-Required: 1');
    }
}

/**
 * Renderiza el formulario de login con los campos indicados.
 */
function render_login_form($username, $ubicacion, $idioma, $esquema, $error, $esquema_web = ''): void
{
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

$esquema_web = getenv('ESQUEMA');
$ubicacion = getenv('UBICACION');
$private = getenv('PRIVATE');

$_SESSION['sfsv'] = $ubicacion;

if (!empty($esquema_web)) {
    $oDBPropiedades = new DBPropiedades();
    $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(false, true);
    if (!in_array($esquema_web, $a_posibles_esquemas)) {
        $msg = sprintf(_('No existe este equema: %s'), $esquema_web);
        die($msg);
    }
}

if (!isset($_SESSION['session_auth'])) {
    orbix_marcar_respuesta_ajax_sin_sesion();
    $idioma = '';

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $_SESSION['private'] = $private;

        $useCase = new LoginProcesar();
        $result = $useCase->execute(
            [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'esquema' => $_POST['esquema'] ?? '',
                'verification_code' => $_POST['verification_code'] ?? '',
            ],
            (string)$esquema_web,
            (string)$ubicacion
        );

        // 2FA pendiente de configuracion -> redirigir a pagina de ayuda.
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
            $error = $result['error'] ?? 1;
            $esquema_form = $_POST['esquema'] ?? $esquema_web;
            render_login_form($_POST['username'], $ubicacion, $idioma, $esquema_form, $error, $esquema_web);
            die();
        }

        // Login OK: rellenar sesion y cookies.
        if (!isset($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = $result['session_auth'];
        }
        if (!isset($_SESSION['config'])) {
            $_SESSION['config'] = $result['session_config'];
        }

        cambiar_idioma();

        $time_expire_cookie = time() + (86400 * 30);
        $arr_cookie_options = [
            'expires' => $time_expire_cookie,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        setcookie('esquema', $result['esquema'], $arr_cookie_options);
        setcookie('idioma', $result['idioma'], $arr_cookie_options);
    } else {
        // Primera visita: pintar el form con cookies previas si existen.
        $esquema = $_COOKIE['esquema'] ?? '';
        $idioma = $_COOKIE['idioma'] ?? '';
        cambiar_idioma($idioma);
        render_login_form('', $ubicacion, $idioma, $esquema, 0, $esquema_web);
        die();
    }
} else {
    // Ya esta registrado; setlocale vive en el proceso, hay que asegurarlo
    // cada request para que gettext traduzca.
    cambiar_idioma();
}

if (!isset($_SESSION['session_go_to'])) {
    $_SESSION['session_go_to'] = 'a';
    // Para que la primera vez vaya a la pagina de inicio personalizada
    // (se mira en index.php):
    $primera = 1;
}
