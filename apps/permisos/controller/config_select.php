<?php
use devel\model as devel;
use permisos\model as permisos;
/**
* Esta página muestra una tabla con las personas que cumplen con la condicion.
*
* Es llamado desde personas_que.php
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		27/8/2007.		
*
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$titulo = 0;

$GesModulos = new devel\GestorModulo();
$cModulos = $GesModulos->getModulos();

$GesModulosInstalados = new permisos\GestorModuloInstalado();
$cModulosInstalados = $GesModulosInstalados->getModulosInstalados();
$aListaModIns = array();
foreach ($cModulosInstalados as $oModuloInstalado) {
	$id_mod = $oModuloInstalado->getId_mod();
	$aListaModIns[] = $id_mod;
}


$a_botones[]=array( 'txt' => _('seleccionar'), 'click' =>"fnjs_ver_equipaje()" ) ;
$a_cabeceras=array(	_("nombre"),
					_("descripción")
				);

$i = 0;
$a_valores = array();
foreach ($cModulos as $oModulo) {
	$i++;
	$id_mod=$oModulo->getId_mod();
	$nom=$oModulo->getNom();
	$descripcion=$oModulo->getDescripcion();

	$chk = (in_array($id_mod,$aListaModIns))? 'checked' : '';

	$a_valores[$i]['sel'] = array('id'=>$id_mod,'select'=>$chk);
	$a_valores[$i][1] = $nom;
	$a_valores[$i][2] = $descripcion;
}
	
$resultado=sprintf( _("Módulos instalados"));

$oHash = new web\Hash();
$oHash->setcamposForm('sel!que!id_dossier');
$oHash->setcamposNo('que!id_dossier');
$a_camposHidden = array(
		'pau' => 'p',
		);
$oHash->setArraycamposHidden($a_camposHidden);
/* ---------------------------------- html --------------------------------------- */
?>
<script>
<?php if (!empty($script['fnjs_modificar'])) { ?>
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/personas/controller/stgr_cambio.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
<?php } ?>
</script>
<h3><?= $resultado ?></h3>
<form id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash->getCamposHtml(); ?>
	<input type='hidden' id='que' name='que' value=''>
	<input type='hidden' id='id_dossier' name='id_dossier' value=''>

<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla("config_select");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<br><table><tr><th>
<span class=link_inv onclick="fnjs_update_div('#main','<?= $pagina ?>');"><?= core\strtoupper_dlb(_("Guardar")) ?></span>
