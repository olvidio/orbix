<?php

namespace frontend\devel_db_admin\controller;

use frontend\devel_db_admin\helpers\DevelDbAdminPayload;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

/**
 * La idea de esta página es poder crear y eliminar
 * las tablas correspondientes a cada app.
 * Al activar un módulo, se debería crear las tablas en el esquema correspondiente,
 * pero por aqui se pueden crear en el esquema global y en otros.
 *
 */


// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$appsPayload = PostRequest::getDataFromUrl('/src/devel_db_admin/apptables_apps_data', []);
$a_apps = DevelDbAdminPayload::desplegableOpciones($appsPayload['a_apps'] ?? []);

$oDeslpApps = new Desplegable([], ['_ordre' => 'id_app']);
$oDeslpApps->setNombre('id_app');
$oDeslpApps->setOpciones($a_apps);

$oHash = new HashFront();
$oHash->setCamposForm('id_app!esquema');
$oHash->setcamposNo('accion');
$oHash->setArraycamposHidden(['accion' => 'x']);

$alerta = _("ojo es un modulo principal");
$a_campos = [
    'oHash' => $oHash,
    'oDesplApps' => $oDeslpApps,
    'alerta' => $alerta,
    'oPosicion' => $oPosicion,
];

$esquema = OrbixRuntime::miRegionDl();
$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'apptables_esquemas',
    'default_esquema' => $esquema,
]);
$a_campos['oDesplEsquemas'] = $dbProps['oDesplEsquemas'] ?? '';

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('apptables.phtml', $a_campos);
