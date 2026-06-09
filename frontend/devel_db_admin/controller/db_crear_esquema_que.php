<?php

namespace frontend\devel_db_admin\controller;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/devel_db_admin/helpers/devel_db_admin_support.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'db_que_esquema_ref',
]);
$oEsquemaRef = $dbProps['oEsquemaRef'] ?? '';
$a_opciones_regiones = devel_db_admin_desplegable_opciones($dbProps['a_opciones_regiones'] ?? []);

$oDesplRegiones = Desplegable::desdeOpciones($a_opciones_regiones, 'region');
$oDesplRegiones->setAction('fnjs_dl()');

$oHash = new HashFront();
$oHash->setCamposForm('esquema!region!dl!comun!sv!sf');
$oHash->setcamposNo('comun!sv!sf');

$oHash1 = new HashFront();
$oHash1->setUrl(OrbixRuntime::getWeb() . '/src/devel_db_admin/db_lugar');
$oHash1->setCamposForm('region');
$h = $oHash1->linkSinValParams();

$msg_falta_dl = _('debe poner la delegación');
$msg_falta_esquema = _('debe elegir el esquema de referencia');

$a_campos = [
    'oHash' => $oHash,
    'h' => $h,
    'oDesplRegiones' => $oDesplRegiones,
    'oEsquemaRef' => $oEsquemaRef,
    'msg_falta_dl' => $msg_falta_dl,
    'msg_falta_esquema' => $msg_falta_esquema,
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('db_crear_esquema_que.phtml', $a_campos);
