<?php
/**
* Esta página sirve para realizar el cambio de stgr de una persona.
*
*
*@package	delegacion
*@subpackage	actividades
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
	$id_sel=$_POST['sel'];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
}

switch ($id_tabla) {
		case "n":
			$obj_pau="PersonaN";
			break;	
		case "x":
			$obj_pau="PersonaNax";
			break;	
		case "a":
			$obj_pau="PersonaAgd";
			break;
		case "s":
			$obj_pau="PersonaS";
			break;	
		case "cp_sss":
			$obj_pau="PersonaSSSC";
			break;	
		case "pn":
		case "pa":
			$obj_pau="PersonaEx";
			break;
}


// según sean numerarios...
$obj = 'personas\\model\\'.$obj_pau;
$oPersona = new $obj($id_nom);

$nom = $oPersona->getNombreApellidos();
$stgr = $oPersona->getStgr();

//posibles valores de stgr
$tipos= array (  "n"=> _("no cursa est."),
				"b"=> _("bienio"),
				"c1"=>  _("cuadrienio año I"),
				"c2"=> _("cuadrienio año II-IV"),
				"r"=> _("repaso"),
				);

//$go_to=stripslashes($go_to);
$oDespl = new web\Desplegable();
$oDespl->setNombre('stgr');
$oDespl->setOpciones($tipos);
$oDespl->setOpcion_sel($stgr);
$oDespl->setBlanco(true);

$oHash = new web\Hash();
$oHash->setcamposForm('stgr');
$a_camposHidden = array(
		'obj_pau' => $obj_pau,
		'id_nom' => $id_nom
		);
$oHash->setArraycamposHidden($a_camposHidden);
?>
<h2 class=titulo><?php echo ucfirst(_("cambiar el stgr")); ?></h2>
<form id="frm_sin_nombre" name="frm_sin_nombre" action="apps/personas/controller/stgr_update.php" method="POST">
	<?= $oHash->getCamposHtml(); ?>
	<table><tr><th class=etiqueta_inv><?php echo $nom; ?></th>
	<td class=datos>
	<?= $oDespl->desplegable(); ?>
	</td></tr>
	</td></tr></table>
	<input type="button" id="guardar" name="guardar" onclick="fnjs_enviar_formulario('#frm_sin_nombre');" value="<?php print(ucfirst(_('guardar'))); ?>" > 
</form>
