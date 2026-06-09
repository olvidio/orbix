<?php

namespace frontend\devel_db_admin\controller;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/devel_db_admin/helpers/devel_db_admin_support.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

// OJO; sólo las que ya tengan el esquema.
$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'db_cambiar_nombre_esquemas',
]);
$a_esquemas_union = devel_db_admin_desplegable_opciones($dbProps['a_esquemas_union'] ?? []);
$a_opciones_regiones = devel_db_admin_desplegable_opciones($dbProps['a_opciones_regiones'] ?? []);

$oDesplEsquemaOrigen = Desplegable::desdeOpciones($a_esquemas_union, 'esquema_origen');
$oDesplRegiones = Desplegable::desdeOpciones($a_opciones_regiones, 'region');
$oDesplRegiones->setAction('fnjs_dl()');

$oHash = new HashFront();
$oHash->setCamposForm('esquema_origen!region!dl!comun!sv!sf');
$oHash->setcamposNo('comun!sv!sf');

$oHash1 = new HashFront();
$oHash1->setUrl(OrbixRuntime::getWeb() . '/src/devel_db_admin/db_lugar');
$oHash1->setCamposForm('region');
$h = $oHash1->linkSinValParams();

$msg_falta_dl = _('debe elegir la delegación de destino');
$msg_falta_region = _('debe elegir la región de destino');
$msg_falta_origen = _('debe elegir el esquema de origen (nombre base antiguo)');

$oHashVerificar = new HashFront();
$oHashVerificar->setUrl(OrbixRuntime::getWeb() . '/frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php');
$oHashVerificar->setCamposForm('esquema_origen!region!dl!comun!sv!sf');
$oHashVerificar->setcamposNo('comun!sv!sf');

$oHashCorregir = new HashFront();
$oHashCorregir->setUrl(OrbixRuntime::getWeb() . '/frontend/devel_db_admin/controller/db_corregir_renombrar_esquema.php');
$oHashCorregir->setCamposForm('esquema_origen!region!dl!comun!sv!sf');
$oHashCorregir->setcamposNo('comun!sv!sf');

$a_campos = [
    'oHash' => $oHash,
    'h' => $h,
    'oDesplEsquemaOrigen' => $oDesplEsquemaOrigen,
    'oDesplRegiones' => $oDesplRegiones,
    'msg_falta_dl' => $msg_falta_dl,
    'msg_falta_region' => $msg_falta_region,
    'msg_falta_origen' => $msg_falta_origen,
    'frmVerificarOculto' => $oHashVerificar->getCamposHtml(),
    'frmCorregirOculto' => $oHashCorregir->getCamposHtml(),
    'url_db_verificar_renombrar' => OrbixRuntime::getWeb() . '/frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php',
    'url_db_corregir_renombrar' => OrbixRuntime::getWeb() . '/frontend/devel_db_admin/controller/db_corregir_renombrar_esquema.php',
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('db_cambiar_nombre_que.phtml', $a_campos);
