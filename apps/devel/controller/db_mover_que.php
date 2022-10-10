<?php

use core\DBPropiedades;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
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

$oDBPropiedades = new DBPropiedades();
$oDBPropiedades->setBlanco(TRUE);
$desplTablas = $oDBPropiedades->posibles_tablas();


$oHash = new web\Hash();
$oHash->setCamposForm('tabla');

$msg_falta_tabla = _("debe poner la tabla");

$a_campos = [
    'oHash' => $oHash,
    'desplTablas' => $desplTablas,
    'msg_falta_tabla' => $msg_falta_tabla,
];

$oView = new core\View('devel/controller');
echo $oView->render('db_mover_que.phtml', $a_campos);