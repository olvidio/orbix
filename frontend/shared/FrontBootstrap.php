<?php

namespace frontend\shared;

use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;

/**
 * Bootstrap de peticiones frontend (controladores AJAX y pantallas parciales).
 *
 * Secuencia: Composer + `.env`, sesión, guardia {@see login.php}, cierre de sesión,
 * validación de hash y creación de {@see Posicion}.
 *
 * Uso típico en un controlador:
 *
 *     require_once 'frontend/shared/FrontBootstrap.php';
 *     $oPosicion = FrontBootstrap::boot();
 */
final class FrontBootstrap
{
    private static bool $composerReady = false;

    private static bool $infrastructureReady = false;

    /**
     * Arranque completo de la petición frontend.
     *
     * Idempotente: varias llamadas en el mismo request no repiten login ni hash.
     */
    public static function boot(?string $scriptSelf = null): Posicion
    {
        self::ensureComposerAndEnv();
        self::ensureInfrastructure();

        return new Posicion(
            $scriptSelf ?? self::requestScriptSelf(),
            $_POST,
        );
    }

    private static function requestScriptSelf(): string
    {
        $phpSelf = $_SERVER['PHP_SELF'] ?? '';

        return is_string($phpSelf) ? $phpSelf : '';
    }

    private static function ensureComposerAndEnv(): void
    {
        if (self::$composerReady) {
            return;
        }

        require_once __DIR__ . '/../../libs/vendor/autoload.php';
        require_once __DIR__ . '/../../src/shared/load_env.php';

        self::$composerReady = true;
    }

    private static function ensureInfrastructure(): void
    {
        if (self::$infrastructureReady) {
            return;
        }

        self::ensureSession();
        self::ensureAuthenticated();
        session_write_close();
        self::validateRequestHash();

        self::$infrastructureReady = true;
    }

    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $sessionName = session_name();
        if (!is_string($sessionName)) {
            $sessionName = 'PHPSESSID';
        }

        $sidFromRequest = '';
        if (!empty($_COOKIE[$sessionName]) && is_string($_COOKIE[$sessionName])) {
            $sidFromRequest = $_COOKIE[$sessionName];
        } elseif (
            !empty($_POST[$sessionName])
            && is_string($_POST[$sessionName])
            && preg_match('/^[a-zA-Z0-9,-]{16,128}$/', $_POST[$sessionName])
        ) {
            // fnjs_link_submenu / fnjs_update_div envían PHPSESSID en el POST AJAX.
            $sidFromRequest = $_POST[$sessionName];
        }

        if ($sidFromRequest !== '') {
            session_id($sidFromRequest);
            session_start();
            if (!isset($_SESSION['session_auth'])) {
                $_SESSION = [];
                session_regenerate_id(true);
            }

            return;
        }

        $timeout = 1800;
        $maxlifetime = time() + $timeout;

        ini_set('session.gc_maxlifetime', (string) $timeout);
        ini_set('session.cookie_lifetime', (string) $timeout);

        session_set_cookie_params([
            'lifetime' => $maxlifetime,
            'Secure' => false,
            'HttpOnly' => true,
            'SameSite' => 'Strict',
        ]);
        session_start();
    }

    private static function ensureAuthenticated(): void
    {
        require_once __DIR__ . '/../usuarios/controller/login.php';
    }

    private static function validateRequestHash(): void
    {
        $oValidator = new HashFront();
        $aData = $_POST !== [] ? $_POST : (isset($_GET['h']) ? $_GET : []);
        echo $oValidator->validatePost($aData);
    }
}
