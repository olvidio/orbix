<?php
use dossiers\model\entity as dossiers;
/**
* Página de visualización de los permisos de los dossiers
* Le llegan las variables $tipo y $id_tipo
*
* Tiene include de ficha.php la cual permite guardar en la tabla 
* d_tipos_dossiers 
*
*@package	delegacion
*@subpackage	system
*@author	Josep Companys
*@since		15/5/02.
*		
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_dataUrl = array('tipo'=>$_POST['tipo']);
$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/perm_dossiers.php?'.http_build_query($a_dataUrl));

?>
<script>
fnjs_eliminar=function(){
   $('#que').val('eliminar');
   $('#frm2').attr('action','apps/dossiers/controller/perm_dossier_update.php');
   fnjs_enviar_formulario('#frm2');
}
fnjs_guardar=function(){
   $('#que').val('guardar');
   $('#frm2').attr('action','apps/dossiers/controller/perm_dossier_update.php');
   fnjs_enviar_formulario('#frm2');
}
</script>
<?php
$oTipoDossier = new dossiers\TipoDossier(array('id_tipo_dossier'=>$_POST['id_tipo_dossier']));
$a_campos['descripcion'] = $oTipoDossier->getDescripcion();
$a_campos['tabla_from'] = $oTipoDossier->getTabla_from();
$a_campos['tabla_to'] = $oTipoDossier->getTabla_to();
$a_campos['campo_to'] = $oTipoDossier->getCampo_to();
$a_campos['id_tipo_dossier_rel'] = $oTipoDossier->getId_tipo_dossier_rel();
$a_campos['permiso_lectura'] = $oTipoDossier->getPermiso_lectura();
$a_campos['permiso_escritura'] = $oTipoDossier->getPermiso_escritura();
$a_campos['depende_modificar'] = $oTipoDossier->getDepende_modificar();
$a_campos['app'] = $oTipoDossier->getApp();
$a_campos['class'] = $oTipoDossier->getClass();

$botones = 0;
/*
1: guardar cambios
2: eliminar
*/
if ($_SESSION['oPerm']->have_perm("admin_sv") OR $_SESSION['oPerm']->have_perm("admin_sf")) { 
	$botones="1,2";
}
$a_campos['botones'] = $botones;
$a_campos['go_to'] = $go_to;

$oView = new core\View('dossiers\controller');
echo $oView->render('perm_dossier_pres.phtml',$a_campos);
