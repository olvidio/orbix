<?php
use ubis\model\entity\GestorRegion;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oGesReg = new GestorRegion();
$oDesplRegiones = $oGesReg->getListaRegiones();
$oDesplRegiones->setNombre('region');
$oDesplRegiones->setAction('fnjs_dl()');

$oHash = new web\Hash();
$oHash->setcamposForm('region!dl!comun!sv!sf');
$oHash->setcamposNo('comun!sv!sf');

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