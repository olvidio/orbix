<?php
/**
* Esta página sirve para comprobar las notas de la tabla e_notas.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		22/11/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$go['comprobar_n']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>'n')));
$go['comprobar_a']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>'a')));
$go['n_listado']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_n.php?'.http_build_query(array('lista'=>1)));
$go['n_numeros']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_n.php?'.http_build_query(array('lista'=>0)));
$go['agd_listado']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_agd.php?'.http_build_query(array('lista'=>1)));
$go['agd_numeros']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_agd.php?'.http_build_query(array('lista'=>0)));
$go['profesores_numeros']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_profesores.php?'.http_build_query(array('lista'=>0)));
$go['profesores_listado']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/informe_stgr_profesores.php?'.http_build_query(array('lista'=>1)));
$go['asig_faltan']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/asig_faltan_que.php');


$a_campos = [
			'go' => $go
			];

$oView = new core\View('notas/model');
echo $oView->render('resumen_anual.phtml',$a_campos);