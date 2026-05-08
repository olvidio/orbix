<?php

namespace frontend\devel_db_admin\controller;

use frontend\shared\PostRequest;
use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\ubis\application\services\RegionDropdown;
use frontend\shared\web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// OJO; sólo las que ya tengan el esquema.
$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'db_que_esquema_ref',
]);
$dbProps = is_array($dbProps) ? $dbProps : [];
$oEsquemaRef = $dbProps['oEsquemaRef'] ?? '';

$oDesplRegiones = Desplegable::desdeOpciones(RegionDropdown::activasOrdenNombre(), 'region');
$oDesplRegiones->setAction('fnjs_dl()');

$oHash = new Hash();
$oHash->setCamposForm('esquema!region!dl!comun!sv!sf');
$oHash->setcamposNo('comun!sv!sf');

$oHash1 = new Hash();
$oHash1->setUrl(ConfigGlobal::getWeb() . '/frontend/devel_db_admin/controller/db_ajax.php');
$oHash1->setCamposForm('salida!entrada');
$h = $oHash1->linkSinValParams();

$msg_falta_dl = _("debe poner la delegación");
$msg_falta_esquema = _("debe poner la delegación de referencia");

$a_campos = [
    'oHash' => $oHash,
    'h' => $h,
    'oDesplRegiones' => $oDesplRegiones,
    'oEsquemaRef' => $oEsquemaRef,
    'msg_falta_dl' => $msg_falta_dl,
    'msg_falta_esquema' => $msg_falta_esquema,
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('db_que.phtml', $a_campos);