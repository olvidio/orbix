<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

	
$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
$Qobj_dir = (string) \filter_input(INPUT_POST, 'obj_dir');
$Qc_p = (string) \filter_input(INPUT_POST, 'c_p');
$Qciudad = (string) \filter_input(INPUT_POST, 'ciudad');
$Qpais = (string) \filter_input(INPUT_POST, 'pais');

$obj = 'ubis\\model\\entity\\Gestor'.$Qobj_dir;
$oGesDir = new $obj();

/*miro las condiciones. las variables son: centro,ciudad */
if (!empty($Qc_p)){
	$aWhere['c_p']=$Qc_p;
	$aOperador['c_p']='LIKE';
}
if (!empty($Qciudad)){
	$aWhere['poblacion']=$Qciudad;
	$aOperador['poblacion']='sin_acentos';
}
if (!empty($Qpais)){
	$aWhere['pais']=$Qpais;
	$aOperador['pais']='sin_acentos';
}

$a_cabeceras=array(_("id"),
                   array('name'=>_("ok"),'formatter'=>'clickFormatter'),
                   _("dirección"),
                   _("cp"),
                   _("ciudad"),
                   _("provincia"),
                   _("ap. Correos"),
                   _("país"),
				   	array('name'=>ucfirst(_("última modif.")),'class'=>'fecha'),
                   _("obervaciones") 
		);
$a_botones=array();
$a_valores=array();
$i=0;
$cDirecciones = $oGesDir->getDirecciones($aWhere,$aOperador);
foreach ($cDirecciones as $oDireccion) {
	$i++;
	$id_direccion=$oDireccion->getId_direccion();
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/ubis/controller/direcciones_asignar.php?'.http_build_query(array('id_ubi'=>$Qid_ubi,'id_direccion'=>$id_direccion,'obj_dir'=>$Qobj_dir,'pais'=>$Qpais))); 
	$a_valores[$i][1]=$id_direccion;
	$a_valores[$i][2]= array( 'ira'=>$pagina, 'valor'=>'ok');
	$a_valores[$i][3]=$oDireccion->getDireccion();
	$a_valores[$i][4]=$oDireccion->getC_p();
	$a_valores[$i][5]=$oDireccion->getPoblacion();
	$a_valores[$i][6]=$oDireccion->getProvincia();
	$a_valores[$i][7]=$oDireccion->getA_p();
	$a_valores[$i][8]=$oDireccion->getPais();
    $a_valores[$i][9]=$oDireccion->getF_direccion();
	$a_valores[$i][10]=$oDireccion->getObserv();
}
 
$url_nueva=web\Hash::link('apps/ubis/controller/direcciones_editar.php?'.http_build_query(array('mod'=>'nuevo','id_ubi'=>$Qid_ubi,'obj_dir'=>$Qobj_dir)));

$oTabla = new web\Lista();
$oTabla->setId_tabla('direcciones_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
			'oTabla' => $oTabla,
			'url_nueva' => $url_nueva,
			];

$oView = new core\View('ubis\controller');
echo $oView->render('direcciones_tabla.phtml',$a_campos);