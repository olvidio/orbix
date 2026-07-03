<?php

namespace frontend\devel_db_admin\controller;

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

/**
 * Para mover una tabla de la DB sv a sv-e que está en la dmz.
 * La nueva está en otro servidor.
 * - copiar la estructura y datos y cambiarlos de servidor.
 * - Hay que cambiar el objeto que la controla
 * - Cambiar la base de datos bucardo que controla la syncronización
 *  con moneders.net
 *
 */

// Posibles tablas existentes en sv:

$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'db_mover_tablas',
]);
$desplTablas = $dbProps['desplTablas'] ?? '';

$oHash = new HashFront();
$oHash->setCamposForm('tabla');

$msg_falta_tabla = _("debe poner la tabla");

$a_campos = [
    'oHash' => $oHash,
    'desplTablas' => $desplTablas,
    'msg_falta_tabla' => $msg_falta_tabla,
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('db_mover_que.phtml', $a_campos);
