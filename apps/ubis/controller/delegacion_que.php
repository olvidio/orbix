<?php
use ubis\model\entity\GestorDelegacion;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$gesDelegacion = new GestorDelegacion();
$oDesplDelegaciones = $gesDelegacion->getListaRegDele(FALSE);
$oDesplDelegaciones->setNombre('dl_destino');
$oDesplDelegaciones->setAction("fnjs_cmb_id_dl()");


$a_campos = [
    'oDesplDelegaciones' => $oDesplDelegaciones,
];

$oView = new core\ViewTwig('ubis/controller');
echo $oView->render('delegaciones.html.twig',$a_campos);
