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

    \src\shared\infrastructure\ui\http\SesionCookie::configurar();
    if (!empty($_COOKIE["PHPSESSID"])) {
        session_id($_COOKIE['PHPSESSID']);
        session_start();
        if (!isset($_SESSION['session_auth'])) {
            $_SESSION = [];
            session_regenerate_id(true);
        }
    } else {
        session_start();
    }
}

require_once('frontend/usuarios/controller/login.php');

// Clave de firma de las cápsulas HashB: se crea con la sesión abierta para que se guarde.
\src\shared\security\HashB::asegurarClave();

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

RefreshCrStgrMaterializedViews::emitUserAvisoIfHtmlRequest();

(new HydrateSessionConfig())->execute();

// Permisos en sesión: carga completa solo la primera vez tras login; luego caché.
(new HydratePermisosActividades())->execute();

require_once __DIR__ . '/../../frontend/shared/bootstrap/after_global_object.inc';
