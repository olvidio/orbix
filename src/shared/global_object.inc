<?php

namespace core;

use src\shared\application\HydratePermisosActividades;
use src\shared\application\HydrateSessionConfig;
use src\shared\application\RefreshCrStgrMaterializedViews;
use src\shared\infrastructure\BootstrapPdoGlobals;
use src\shared\infrastructure\ConnectionBootstrap;
use src\shared\infrastructure\DiContainerBootstrap;
use src\shared\infrastructure\logging\GestorErrores;

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Para detectar el error: "Headers already sent"
    //$file = $line = null; headers_sent($file, $line); die("$file:$line");

    if (!empty($_COOKIE["PHPSESSID"])) {
        session_id($_COOKIE['PHPSESSID']);
        session_start();
        if (!isset($_SESSION['session_auth'])) {
            $_SESSION = [];
            session_regenerate_id(true);
        }
    } else {
        // Configure timeout to 30 minutes
        $timeout = 1800;
        $maxlifetime = time() + $timeout;

        // Set the maxlifetime of session
        ini_set("session.gc_maxlifetime", $timeout);

        // Also set the session cookie timeout
        ini_set("session.cookie_lifetime", $timeout);

        //$domain = ConfigGlobal::getDomain();
        // Now start the session
        session_set_cookie_params([
            'lifetime' => $timeout,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
            //'Domain' => $domain
        ]);
        session_start();
    }
}

require_once('frontend/usuarios/controller/login.php');

$_SESSION['oGestorErrores'] = new GestorErrores();

$connectionBootstrap = ConnectionBootstrap::buildFromSession();
BootstrapPdoGlobals::register($connectionBootstrap->userSfsv, $connectionBootstrap->connections);

DiContainerBootstrap::ensureBuilt();

(new RefreshCrStgrMaterializedViews())->executeIfNeeded(
    $connectionBootstrap->userSfsv,
    $connectionBootstrap->esquema,
    $connectionBootstrap->esquemav,
    $connectionBootstrap->esquemaf
);

(new HydrateSessionConfig())->execute();

// func_tablas. Es necesaria para permisos\PermisosActividades->carregar()...
include_once(__DIR__ . '/../../src/shared/domain/helpers/func_tablas.php');

// Permisos en sesión: carga completa solo la primera vez tras login; luego caché.
(new HydratePermisosActividades())->execute();

require_once __DIR__ . '/../../frontend/shared/bootstrap/after_global_object.inc';
