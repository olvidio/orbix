<?php
namespace ubis\controller;
use core;
use usuarios\model as usuarios;
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

$tipo_ubi = empty($_POST['tipo_ubi'])? '' : $_POST['tipo_ubi'];

if (!empty($_POST['nuevo'])) {
	$Gestor = unserialize(core\urlsafe_b64decode($_POST['sGestor']));
	$obj = str_replace('Gestor','',$Gestor);
	$oUbi = new $obj();
	$obj_pau = str_replace('ubis\\model\\','',$obj);
	$cDatosCampo = $oUbi->getDatosCampos();
	$oDbl = $oUbi->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;
	}
	$nombre_ubi = empty($_POST['nombre_ubi'])? '' : $_POST['nombre_ubi'];
	$a_campos['nombre_ubi'] = urldecode($nombre_ubi);
	$a_campos['id_ubi'] = '';
	$a_campos['id_direccion'] = '';
	//print_r($a_campos);
} else {
	$obj = 'ubis\\model\\'.$_POST['obj_pau'];
	$oUbi = new $obj($_POST['id_ubi']);
	$obj_pau = $_POST['obj_pau'];
	$a_campos = $oUbi->getTot();
	$a_campos['id_direccion'] = '';
}

$sf = $oUbi->getSf();

$a_campos['tipo_ubi'] = $tipo_ubi;
$a_campos['obj_pau'] = $obj_pau;
//----------------------------------Permisos según el usuario
$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

$botones = 0;
/*
1: guardar cambios
2: eliminar
4: quitar direccion
*/
if (strstr($obj_pau,'Dl')) {
	if ($a_campos['dl'] == core\ConfigGlobal::mi_dele()) {
		// ----- sv sólo a scl -----------------
		if ($_SESSION['oPerm']->have_perm("scdl")) {
					$botones= "1,2";
		}
	}
} else if (strstr($obj_pau,'Ex')) {
	// ----- sv sólo a scl -----------------
	if ($_SESSION['oPerm']->have_perm("scdl")) {
				$botones= "1,2";
	}
}

$a_campos['botones'] = $botones;
//------------------------------------------------------------------------
?>
<script>
fnjs_guardar=function(){
   var error=0;
   var tipo_ubi=$("#tipo_ubi").val();
   if (tipo_ubi=="cdcdl" || tipo_ubi=="cdcex") {
	   var camp_sf="#sf:checked";
	   var camp_sv="#sv:checked";
	   var val_sf=$(camp_sf).length;
	   var val_sv=$(camp_sv).length;
	   if (!val_sf && !val_sv) {
	   		alert ("<?= _("debe indicar si es sf o sv") ?>");
			error=1;
		}
   } 
   if (!error) {
	   $('#que').val('ubi');
	   $('#frm2').attr('action','apps/ubis/controller/ubis_update.php');
	   fnjs_enviar_formulario('#frm2','#ficha_ubis');
   }
}

fnjs_eliminar=function(r,go){
	if (confirm("<?php echo _("¿Esta seguro que desea borrar este ubi?");?>") ) {
	   $('#que').val('eliminar_ubi');
	   $('#frm2').attr('action','apps/ubis/controller/ubis_update.php');
	   fnjs_enviar_formulario('#frm2');
	}
}

fnjs_quitar_dir=function(go){
   $('#go_to').val(go);
   $('#frm2').attr('action','programas/direcciones_quitar.php');
   fnjs_enviar_formulario('#frm2');
}
</script>
<?php
$oView = new core\View(__NAMESPACE__);
echo $oView->render('ubis_form.phtml',$a_campos);
?>
