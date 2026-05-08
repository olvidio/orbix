<?php

namespace frontend\devel_db_admin\controller;

use frontend\shared\PostRequest;
use src\shared\config\ConfigGlobal;
use src\ubis\application\services\RegionDropdown;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// OJO; sólo las que ya tengan el esquema.
$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'db_cambiar_nombre_esquemas',
]);
$dbProps = is_array($dbProps) ? $dbProps : [];
$oEsquemaRef = $dbProps['oEsquemaRef'] ?? '';
$a_posibles_esquemas = (array)($dbProps['a_posibles_esquemas'] ?? []);

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

// absorber
$oDesplMatriz = new Desplegable();
$oDesplMatriz->setNombre('esquema_matriz');
$oDesplMatriz->setBlanco(TRUE);
$oDesplMatriz->setOpciones($a_posibles_esquemas);

$oDesplDel = new Desplegable();
$oDesplDel->setNombre('esquema_del');
$oDesplDel->setBlanco(TRUE);
$oDesplDel->setOpciones($a_posibles_esquemas);

$oHashAbsorber = new Hash();
$oHashAbsorber->setCamposForm('esquema_matriz!esquema_del');

$a_campos = [
    'oHash' => $oHash,
    'h' => $h,
    'oDesplRegiones' => $oDesplRegiones,
    'oEsquemaRef' => $oEsquemaRef,
    'msg_falta_dl' => $msg_falta_dl,
    'msg_falta_esquema' => $msg_falta_esquema,
    // absorber
    'oDesplMatriz' => $oDesplMatriz,
    'oDesplDel' => $oDesplDel,
    'oHashAbsorber' => $oHashAbsorber,
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('db_cambiar_nombre_que.phtml', $a_campos);