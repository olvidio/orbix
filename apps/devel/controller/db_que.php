<?php

use core\DBPropiedades;
use ubis\model\entity\GestorRegion;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// OJO; sólo las que ya tengan el esquema.
$oDBPropiedades = new DBPropiedades();
$oDBPropiedades->setBlanco(TRUE);
$oEsquemaRef = $oDBPropiedades->posibles_esquemas('');

$oGesReg = new GestorRegion();
$oDesplRegiones = $oGesReg->getListaRegiones();
$oDesplRegiones->setNombre('region');
$oDesplRegiones->setAction('fnjs_dl()');

$oHash = new web\Hash();
$oHash->setCamposForm('esquema!region!dl!comun!sv!sf');
$oHash->setcamposNo('comun!sv!sf');

$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb() . '/apps/devel/controller/db_ajax.php');
$oHash1->setCamposForm('salida!entrada');
$h = $oHash1->linkSinVal();

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

$oView = new core\View('devel/controller');
$oView->renderizar('db_que.phtml', $a_campos);