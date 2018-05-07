<?php
use dossiers\model\entity as dossiers;

$aWhere = array('tabla_from'=>$pau);
$GesTipoDossier = new dossiers\GestorTipoDossier();
$cTipoDossier = $GesTipoDossier->getTiposDossiers($aWhere); 
$i=0;
$a_filas = array();
$oPermDossier = new dossiers\PermDossier();
foreach ($cTipoDossier as $oTipoDossier) {
	extract($oTipoDossier->getTot());
	$id_dossier = $id_tipo_dossier;
	// Miro si la app está instalada
	if(!core\ConfigGlobal::is_app_installed($app)) continue;
	$aWhere = array('tabla'=>$tabla_from,'id_pau'=>$id_pau,'id_tipo_dossier'=>$id_tipo_dossier,'_ordre'=>'id_tipo_dossier');
	$oDossier = new dossiers\Dossier($aWhere);
	$status_dossier = $oDossier->getStatus_dossier();
	switch ($status_dossier) {
		case "t":
			$a_filas[$i]['imagen'] = core\ConfigGlobal::$web_icons.'/folder.open.gif';
			break;
		case "f":
		default:
			$a_filas[$i]['imagen'] = core\ConfigGlobal::$web_icons.'/folder.gif';
			break;
	}
	$a_filas[$i]['clase'] = $i % 2  ? 'imp' : 'par';
	$a_filas[$i]['descripcion'] = $descripcion;
	$perm_a=$oPermDossier->permiso($permiso_lectura,$permiso_escritura,$depende_modificar,$pau,$id_pau);
	 
	$a_filas[$i]['href_ver']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$Qobj_pau,'id_dossier'=>$id_dossier,'permiso'=>$perm_a,'depende'=>$depende_modificar)));
	$a_filas[$i]['href_abrir']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossier_abrir.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$Qobj_pau,'id_dossier'=>$id_dossier,'tabla_to'=>$tabla_to,'permiso'=>$perm_a)));
	$a_filas[$i]['perm_a'] = $perm_a;
	$i++;
}

$oView = new core\View('dossiers\controller');
echo $oView->render('lista_dossiers.phtml',array('a_filas'=>$a_filas));
?>
