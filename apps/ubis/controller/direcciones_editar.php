<?php
use usuarios\model as usuarios;
use ubis\model as ubis;
/**
* Es el frame inferior. Muestra la ficha de los ubis
*
* Se incluye la página ficha.php que contiene la función ficha.
* Esta página sirve para definir los parámetros que se le pasan a la función ficha.
*
*@package	delegacion
*@subpackage	ubis
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

switch ($_POST['obj_dir']) {
	case 'DireccionCdc': // tipo dl pero no de la mia
		$obj_x = 'ubis\\model\\CdcxDireccion';
		$obj_ubi = 'ubis\\model\\Casa';
		break;
	case 'DireccionCdcDl':
		$obj_x = 'ubis\\model\\CdcDlxDireccion';
		$obj_ubi = 'ubis\\model\\CasaDl';
		break;
	case 'DireccionCdcEx':
		$obj_x = 'ubis\\model\\CdcExxDireccion';
		$obj_ubi = 'ubis\\model\\CasaEx';
		break;
	case 'DireccionCtr': // tipo dl pero no de la mia
		$obj_x = 'ubis\\model\\CtrxDireccion';
		$obj_ubi = 'ubis\\model\\Centro';
		break;
	case 'DireccionCtrDl':
		$obj_x = 'ubis\\model\\CtrDlxDireccion';
		$obj_ubi = 'ubis\\model\\CentroDl';
		break;
	case 'DireccionCtrEx':
		$obj_x = 'ubis\\model\\CtrExxDireccion';
		$obj_ubi = 'ubis\\model\\CentroEx';
		break;
}
$obj = 'ubis\\model\\'.$_POST['obj_dir'];

if (isset($_POST['mod']) && $_POST['mod'] == 'nuevo' ) {
	$oUbi = new $obj();
	$cDatosCampo = $oUbi->getDatosCampos();
	$oDbl = $oUbi->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;

	}
	$a_campos['obj_dir'] = $_POST['obj_dir'];
	$a_campos['id_ubi'] = empty($_POST['id_ubi'])? '' : $_POST['id_ubi'];
	$a_campos['idx'] = 'nuevo';
	$a_campos['id_direccion'] = '';
	//print_r($a_campos);
	$golistadir = '';
	$quitardir = '';
} else {
	// puede haber más de una dirección
	$a_id_direccion = explode(',',$_POST['id_direccion']);
	$num_dir = count($a_id_direccion);
	$idx = empty($_POST['idx'])? 0 : $_POST['idx'];
	$inc = empty($_POST['inc'])? '' : $_POST['inc'];

	if ($inc == 'mas' & $idx < $num_dir-1) $idx++;
	if ($inc == 'menos' & $idx > 0) $idx--;

	$id_direccion_actual = $a_id_direccion[$idx];
	$oDireccion = new $obj($a_id_direccion[$idx]);

	$xDireccion = new $obj_x(array('id_ubi'=>$_POST['id_ubi'],'id_direccion'=>$a_id_direccion[$idx]));

	$a_campos = $oDireccion->getTot();
	$a_campos['propietario'] = $xDireccion->getPropietario();
	$a_campos['principal'] = $xDireccion->getPrincipal();

	$oUbi = new $obj_ubi($_POST['id_ubi']);
	$sf = $oUbi->getSf();
	$dl = $oUbi->getDl();
	$tipo_ubi = $oUbi->getTipo_ubi();

	$a_campos['mas'] = ($idx < $num_dir-1)? 1 : 0; 
	$a_campos['menos'] = ($idx < 1)? 0 : 1; 
	$a_campos['obj_dir'] = $_POST['obj_dir'];
	$a_campos['idx'] = $idx;
	$a_campos['id_direccion'] = $_POST['id_direccion'];
	$a_campos['id_direccion_actual'] = $id_direccion_actual;
	$a_campos['id_ubi'] = $_POST['id_ubi'];

	$golistadir = web\Hash::link("apps/ubis/controller/direcciones_que.php?id_ubi=".$_POST['id_ubi']."&id_direccion=".$_POST['id_direccion']."&obj_dir=".$_POST['obj_dir']);
	$quitardir = web\Hash::link("apps/ubis/controller/direcciones_quitar.php?id_ubi=".$_POST['id_ubi']."&id_direccion=".$_POST['id_direccion']."&obj_dir=".$_POST['obj_dir']."&hno=idx");
}

//----------------------------------Permisos según el usuario

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

$botones = 0;
/*
1: guardar cambios
2: eliminar
3: eliminar
4: quitar direccion
*/
if (strstr($_POST['obj_dir'],'Dl')) {
	if ($dl == core\ConfigGlobal::mi_dele()) {
		// ----- sv sólo a scl -----------------
		if ($_SESSION['oPerm']->have_perm("scdl")) {
					$botones= "1,4,5";
		}
	}
} else if (strstr($_POST['obj_dir'],'Ex')) {
	// ----- sv sólo a scl -----------------
	if ($_SESSION['oPerm']->have_perm("scdl")) {
				$botones= "1,4,5";
	}
}
$a_campos['botones'] = $botones;
//------------------------------------------------------------------------

if (empty($_POST['mod']) & empty($_POST['id_direccion'])) {
 	?>
	<table><tr><td><?= _("Este ubi no dispone de una dirección. Compruebe primero si existe, en este caso, asígnesela. En caso contrario cree una nueva.") ?></td></tr></table>
	<br>
	<span class="link" onclick="fnjs_update_div('#ficha_ubis','<?= $golistadir ?>');">
	<?=  mb_strtoupper(_('asignar una direccion')) ?>
	</span>
	<?php
	exit;
}

?>
<script>
fnjs_guardar_dir=function(){
   $('#que').val('direccion');
   $('#frm2').attr('action','apps/ubis/controller/ubis_update.php');
   fnjs_enviar_formulario('#frm2');
}

fnjs_nuevo=function(f,go){
   $('#onanar').val(f);
   $('#go_to').val(go);
   $('#frm2').attr('action','programas/ficha_nueva.php');
   fnjs_enviar_formulario('#frm2');
}

fnjs_eliminar=function(f,r,go){
	alert ("¿Está seguro que desea eliminar esta ficha?");
   $('#onanar').val(f);
   $('#b').val(r);
   $('#go_to').val(go);
   $('#frm2').attr('action','programas/ficha_eliminar.php');
   fnjs_enviar_formulario('#frm2');
}

fnjs_quitar_dir=function(idx){
	url ='<?= $quitardir ?>'+'&idx='+idx;
   fnjs_update_div('#ficha_ubis',url);
}

fnjs_add_dir=function(){
   fnjs_update_div('#ficha_ubis','<?= $golistadir ?>');
}

</script>
<?php

$oView = new core\View('ubis\controller');
echo $oView->render('direccion_form.phtml',$a_campos);

?>
