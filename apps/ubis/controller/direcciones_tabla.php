<?php
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

	
$obj = 'ubis\\model\\Gestor'.$_POST['obj_dir'];
$oGesDir = new $obj();

/*miro las condiciones. las variables son: centro,ciudad */
if (!empty($_POST['c_p'])){
	$aWhere['c_p']=$_POST['c_p'];
	$aOperador['c_p']='LIKE';
}
if (!empty($_POST['ciudad'])){
	$aWhere['poblacion']=$_POST['ciudad'];
	$aOperador['poblacion']='sin_acentos';
}
if (!empty($_POST['pais'])){
	$aWhere['pais']=$_POST['pais'];
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
	$pagina=web\Hash::link(core\ConfigGlobal::getWeb()."/apps/ubis/controller/direcciones_asignar.php?id_ubi=${_POST['id_ubi']}&id_direccion=$id_direccion&obj_dir=${_POST['obj_dir']}"); 
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
 
$pagina=web\Hash::link("apps/ubis/controller/direcciones_editar.php?mod=nuevo&id_ubi=${_POST['id_ubi']}&obj_dir=${_POST['obj_dir']}"); 
?>
<h2 class=titulo><?php echo ucfirst(_("tabla de direcciones")); ?></h2>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('direcciones_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
<br><h3><span class="link" onclick="fnjs_update_div('#main','<?= $pagina; ?>');"><?= _("crear otra nueva"); ?></span></h3>
