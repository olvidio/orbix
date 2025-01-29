<?php

use core\ConfigGlobal;
use core\DBPropiedades;
use core\ViewPhtml;
use devel\model\entity\GestorApp;
use web\Desplegable;
use web\Hash;

/**
 * La idea de esta página es poder crear y eliminar
 * las tablas correspondientes a cada app.
 * Al activar un módulo, se debería crear las tablas en el esquema correspondiente,
 * pero por aqui se pueden grear en el esquema global y en otros.
 *
 */


// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oGesApps = new GestorApp();
$cApps = $oGesApps->getApps();
$a_apps = [];
foreach ($cApps as $oApp) {
    $id_app = $oApp->getId_app();
    $nom_app = $oApp->getNom();
    $a_apps[$id_app] = $nom_app;
}

$oDeslpApps = new Desplegable([], ['_ordre' => 'id_app']);
$oDeslpApps->setNombre('id_app');
$oDeslpApps->setOpciones($a_apps);

$oHash = new Hash();
$oHash->setCamposForm('id_app!esquema');
$oHash->setcamposNo('accion');
$oHash->setArraycamposHidden(['accion' => 'x']);

$alerta = _("ojo es un modulo principal");
$a_campos = [
    'oHash' => $oHash,
    'oDesplApps' => $oDeslpApps,
    'alerta' => $alerta,
];

$oDBPropiedades = new DBPropiedades();
$esquema = ConfigGlobal::mi_region_dl();
$a_campos['oDesplEsquemas'] = $oDBPropiedades->posibles_esquemas($esquema);


$oView = new ViewPhtml('devel\controller');
$oView->renderizar('apptables.phtml', $a_campos);