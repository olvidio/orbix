<?php

namespace frontend\shared;

use frontend\shared\security\HashF;
use frontend\shared\web\Posicion;
use src\shared\application\RefreshCrStgrMaterializedViews;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\BootstrapPdoGlobals;
use src\shared\infrastructure\ConnectionBootstrap;
use src\shared\infrastructure\DiContainerBootstrap;
use src\shared\infrastructure\logging\GestorErrores;

/**
 * Bootstrap de peticiones frontend (controladores AJAX y pantallas parciales).
 *
 * Secuencia: Composer + `.env`, sesión, guardia {@see login.php}, refresh de vistas
 * materializadas cr-stgr (H-Hv / M-Mv), cierre de sesión, validación de hash y
 * creación de {@see Posicion}.
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

    private static bool $backendBootstrapReady = false;

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
        self::ensureBackendBootstrap();
        self::ensureCrStgrMaterializedViews();
        // Clave de firma de las cápsulas HashB: antes de cerrar la sesión, para que se guarde.
        \src\shared\security\HashB::asegurarClave();
        session_write_close();
        \src\shared\infrastructure\ui\http\AccesoRolPau::comprobarPeticionActual();
        self::validateRequestHash();

        self::$infrastructureReady = true;
    }

    /**
     * Al autenticarse: id de sesión nuevo y clave de firma nueva. Lo que hubiera antes de entrar (un id
     * fijado por otro, cápsulas firmadas) deja de valer.
     */
    public static function renovarSesionAlEntrar(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }
        \src\shared\security\HashB::renovarClave();
    }

    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        \src\shared\infrastructure\ui\http\SesionCookie::configurar();

        // Tras session_write_close() en el mismo request (p. ej. global_object /
        // after_global_object), session_id() sigue disponible: reabrir esa sesión
        // en lugar de crear una vacía (falla en el primer login con página de inicio
        // que llama a FrontBootstrap::boot(), p. ej. avisos de cambios).
        $sidFromCurrentRequest = session_id();
        if (is_string($sidFromCurrentRequest) && $sidFromCurrentRequest !== '') {
            session_start();

            return;
        }

        $sessionName = session_name();
        if (!is_string($sessionName)) {
            $sessionName = 'PHPSESSID';
        }

        $sidFromRequest = '';
        if (!empty($_COOKIE[$sessionName]) && is_string($_COOKIE[$sessionName])) {
            $sidFromRequest = $_COOKIE[$sessionName];
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

        session_start();
    }

    private static function ensureAuthenticated(): void
    {
        require_once __DIR__ . '/../usuarios/controller/login.php';
    }

    /**
     * PDO global y contenedor PHP-DI para controladores que usan {@see DependencyResolver}
     * sin pasar por {@see PostRequest} (p. ej. subida multipart directa).
     */
    private static function ensureBackendBootstrap(): void
    {
        if (self::$backendBootstrapReady) {
            return;
        }

        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            return;
        }

        if (!isset($_SESSION['oGestorErrores'])) {
            $_SESSION['oGestorErrores'] = new GestorErrores();
        }

        try {
            $connectionBootstrap = ConnectionBootstrap::buildFromSession();
        } catch (\Throwable $e) {
            error_log('[FrontBootstrap] ConnectionBootstrap: ' . $e->getMessage());

            return;
        }

        BootstrapPdoGlobals::register($connectionBootstrap->userSfsv, $connectionBootstrap->connections);
        DiContainerBootstrap::ensureBuilt();
        self::$backendBootstrapReady = true;
        \src\shared\infrastructure\ui\http\SesionVigencia::revalidarUsuarioDeLaSesion();
    }

    /**
     * Esquemas región STGR (p. ej. H-Hv, M-Mv): crear/refrescar vistas materializadas
     * antes de `PostRequest` a `/src/...` (antes lo hacía `global_object.inc` en apps/).
     */
    private static function ensureCrStgrMaterializedViews(): void
    {
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            return;
        }

        if (ConfigGlobal::mi_region() !== ConfigGlobal::mi_delef()) {
            return;
        }

        self::ensureBackendBootstrap();
        if (!self::$backendBootstrapReady) {
            return;
        }

        try {
            $connectionBootstrap = ConnectionBootstrap::buildFromSession();
        } catch (\Throwable $e) {
            error_log('[FrontBootstrap] ConnectionBootstrap: ' . $e->getMessage());

            return;
        }

        (new RefreshCrStgrMaterializedViews())->executeIfNeeded(
            $connectionBootstrap->userSfsv,
            $connectionBootstrap->esquema,
            $connectionBootstrap->esquemav,
            $connectionBootstrap->esquemaf,
        );

        RefreshCrStgrMaterializedViews::emitUserAvisoIfHtmlRequest();
    }

    private static function validateRequestHash(): void
    {
        (new HashF())->validatePost(HashF::datosDeLaPeticion());
    }
}
