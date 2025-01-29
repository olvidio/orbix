<?php

use core\ConfigGlobal;
use core\DBPropiedades;
use core\ViewPhtml;
use ubis\model\entity\GestorRegion;
use web\Desplegable;
use web\Hash;

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

$oHash = new Hash();
$oHash->setCamposForm('esquema!region!dl!comun!sv!sf');
$oHash->setcamposNo('comun!sv!sf');

$oHash1 = new Hash();
$oHash1->setUrl(ConfigGlobal::getWeb() . '/apps/devel/controller/db_ajax.php');
$oHash1->setCamposForm('salida!entrada');
$h = $oHash1->linkSinVal();

$msg_falta_dl = _("debe poner la delegación");
$msg_falta_esquema = _("debe poner la delegación de referencia");

// absorber
$a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE);

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

$oView = new ViewPhtml('devel/controller');
$oView->renderizar('db_cambiar_nombre_que.phtml', $a_campos);