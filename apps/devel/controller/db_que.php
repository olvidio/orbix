<?php
use ubis\model\entity as ubis;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oGesReg = new ubis\GestorRegion();
$oDesplRegiones = $oGesReg->getListaRegiones();
$oDesplRegiones->setNombre('region');
$oDesplRegiones->setAction('fnjs_dl()');

$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegaciones = $oGesDl->getListaDelegaciones();
$oDesplDelegaciones->setNombre('dl');


$oHash = new web\Hash();
$oHash->setcamposForm('region!dl!sv!sf');
$oHash->setcamposNo('sv!sf');

$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb().'/apps/devel/controller/db_ajax.php');
$oHash1->setCamposForm('salida!entrada'); 
$h = $oHash1->linkSinVal();


$a_campos = [
			'oHash' => $oHash,
			'h' => $h,
			'oDesplRegiones' => $oDesplRegiones,
			];

$oView = new core\View('devel/controller');
echo $oView->render('db_que.phtml',$a_campos);