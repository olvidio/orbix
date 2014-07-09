<?php
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

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$s_pkey=explode('#',$_POST['sel'][0]);
	// he cambiado las comillas dobles por simples. Deshago el cambio.
	$s_pkey = str_replace("'",'"',$s_pkey[0]);
	$a_pkey=unserialize(core\urlsafe_b64decode($s_pkey));
} else { // si es nuevo
	$s_pkey='';
}

$obj_pau = $_POST['obj_pau'];

switch ($obj_pau) {
	case 'Centro': // tipo dl pero no de la mia
		$obj = 'ubis\\model\\TelecoCtr';
		break;
	case 'CentroDl':
		$obj = 'ubis\\model\\TelecoCtrDl';
		break;
	case 'CentroEx':
		$obj = 'ubis\\model\\TelecoCtrEx';
		break;
	case 'Casa': // tipo dl pero no de la mia
		$obj = 'ubis\\model\\TelecoCdc';
		break;
	case 'CasaDl':
		$obj = 'ubis\\model\\TelecoCdcDl';
		break;
	case 'CasaEx':
		$obj = 'ubis\\model\\TelecoCdcEx';
		break;
}

if (!empty($_POST['mod']) & $_POST['mod'] == 'nuevo' ) {
	$oUbi = new $obj();
	$cDatosCampo = $oUbi->getDatosCampos();
	$oDbl = $oUbi->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;

	}
	$a_campos['id_ubi'] = empty($_POST['id_ubi'])? '' : $_POST['id_ubi'];
	$a_campos['id_item'] = '';
	//print_r($a_campos);
} else {
	$oUbi = new $obj($a_pkey);
	$a_campos = $oUbi->getTot();
}

$a_campos['obj_pau'] = $obj_pau;
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
switch($obj_pau) {
	case 'CentroDl':
	case 'CasaDl':
		$objfull = 'ubis\\model\\'.$obj_pau;
		$oUbi = new $objfull($_POST['id_ubi']);
		$dl = $oUbi->getDl();
		if ($dl == core\ConfigGlobal::mi_dele()) {
			// ----- sv sólo a scl -----------------
			if ($_SESSION['oPerm']->have_perm("scdl")) {
						$botones= "1,3";
			}
		}
	break;
	case 'CentroEx':
	case 'CasaEx':
		// ----- sv sólo a scl -----------------
		if ($_SESSION['oPerm']->have_perm("scdl")) {
					$botones= "1,3";
		}
	break;
}
$a_campos['botones'] = $botones;
//------------------------------------------------------------------------
?>
<script>
fnjs_guardar=function(){
   var error=0;
   $('#mod').val('teleco');
   $('#frm2').attr('action','apps/ubis/controller/teleco_update.php');
   fnjs_enviar_formulario('#frm2','#ficha_ubis');
}
</script>
<?php
$oView = new core\View('ubis\controller');
echo $oView->render('teleco_form.phtml',$a_campos);
?>
