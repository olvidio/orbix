<?php
use actividadcargos\model as actividadcargos;
use personas\model as personas;
/**
 * Muestra un formulario para introducir/cambiar los datos del objeto ActividadCargo
 * Si se crea un nuevo cargo, machaca el anterior (si lo hubiere)
 * Si se cambia el cargo, crea uno nuevo, no elimina el anterior.
 *
 * @package	delegacion
 * @subpackage	actividades
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @ajax		23/8/2007.
 * @version 1.0
 * @created 25/09/2010
 *
 * @param array $_POST['sel'] con id_nom#id_cargo si vengo de un select de una lista
 * @param string $_POST['go_to'] página a la que ir al terminar la acción.
 * @param integer $_POST['id_activ']
 * @param integer $_POST['id_cargo']
 * @param integer $_POST['id_nom']
 * @param string $_POST['observ'] optional
 * @param boolean $_POST['puede_agd'] 
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$go_to = (string)  \filter_input(INPUT_POST, 'go_to');
if (!empty($go_to)) {
	$go_to=urldecode($go_to);
}
	
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$id_nom = strtok($a_sel[0],"#");
	$id_cargo=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,0);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,0);
	if (!empty($go_to)) {
		// add stack:
		$stack = $oPosicion->getStack();
		$go_to .= "&stack=$stack";
	}
} else {
	$id_nom = empty($_POST['id_nom'])? "" : $_POST['id_nom'];
}

$id_activ = (integer)  \filter_input(INPUT_POST, 'id_pau');

$obj = 'actividadcargos\\model\\ActividadCargo';

$permiso =	empty($_POST['permiso'])? '' : $_POST['permiso'];

if (!empty($id_nom)) { //caso de modificar
	$oPersona=personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $id_nom";
		exit ($msg_err);
	}
	$ape_nom=$oPersona->getApellidosNombre();
	$id_tabla=$oPersona->getId_tabla();
	$id_nom_real=$id_nom;
	$oActividadCargo=new actividadcargos\ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
	$puede_agd=$oActividadCargo->getPuede_agd();
	$observ=$oActividadCargo->getObserv();
} else { //caso de nuevo cargo
	$observ="";
	if (!empty($_POST['obj_pau'])) {
		$_POST['obj_pau'] = !empty($_POST['obj_pau'])? urldecode($_POST['obj_pau']) : '';
		$obj_pau = strtok($_POST['obj_pau'],'&');
		$na = strtok('&');
		$na_txt = strtok($na,'=');
		$na_val = 'p'.strtok('=');
		switch ($obj_pau) {
			case 'PersonaN':
				$oPersonas=new personas\GestorPersonaN();
				$oDesplegablePersonas = $oPersonas->getListaPersonas();
				$oDesplegablePersonas->setNombre('id_nom');
				break;
			case 'PersonaNax':
				$oPersonas=new personas\GestorPersonaNax();
				$oDesplegablePersonas = $oPersonas->getListaPersonas();
				$oDesplegablePersonas->setNombre('id_nom');
				break;
			case 'PersonaAgd':
				$oPersonas=new personas\GestorPersonaAgd();
				$oDesplegablePersonas = $oPersonas->getListaPersonas();
				$oDesplegablePersonas->setNombre('id_nom');
				break;
			case 'PersonaS':
				$oPersonas=new personas\GestorPersonaS();
				$oDesplegablePersonas = $oPersonas->getListaPersonas();
				$oDesplegablePersonas->setNombre('id_nom');
				break;
			case 'PersonaSSSC':
			case 'PersonaEx':
				$oPersonas=new personas\GestorPersonaEx();
				$oDesplegablePersonas = $oPersonas->getListaPersonas($na_val);
				$oDesplegablePersonas->setNombre('id_nom');
				$obj_pau = 'PersonaEx';
				break;
		}
	} else {
		//$go_to=urlencode(core\core\core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php?pau=$pau&id_pau=$id_pau&id_dossier=$id_dossier&permiso=$permiso");
		$go_to=urlencode(core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php?pau=$pau&id_pau=$id_pau&id_dossier=$id_dossier&permiso=$permiso");
		$oPosicion = new web\Posicion();
		echo $oPosicion->ir_a($go_to);
	}
}
$oCargos=new actividadcargos\GestorCargo();
//$aOpciones=$oCargos->getCargos();
//$oDesplegableCargos=new web\Desplegable('id_cargo',$aOpciones,$id_cargo,false);
$oDesplegableCargos=$oCargos->getListaCargos();
$oDesplegableCargos->setNombre('id_cargo');
$oDesplegableCargos->setBlanco(false);
$oDesplegableCargos->setopcion_sel($id_cargo);
$chk = (!empty($puede_agd) && $puede_agd=='t')? 'checked' : '' ;


$oHash = new web\Hash();
$camposForm = 'id_cargo!observ';
$camposNo = 'puede_agd!scroll_id';
$a_camposHidden = array(
		'id_activ' => $_POST['id_pau'],
		'mod'=> $_POST['mod'],
		'go_to' => $go_to
		);
		//'obj_pau'=> $obj_pau,
if (!empty($id_nom_real)) {
	$a_camposHidden['id_nom'] = $id_nom_real;
} else {
	if ($_POST['mod']=="nuevo") {
		$camposNo .= '!asis';
	}
	$camposForm .= '!id_nom';
}
$oHash->setCamposNo($camposNo);
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

echo $oPosicion->mostrar_left_slide();
?>
<!-- ------------------- html -----------------------------------------------  -->
<script>
fnjs_guardar=function(formulario){
	var rr=fnjs_comprobar_campos(formulario,'<?= addslashes($obj) ?>');
	//alert ("EEE "+rr);
	if (rr=='ok') {
		go=$('#go_to').val();
		$(formulario).attr('action',"apps/actividadcargos/controller/update_3102.php");
		$(formulario).submit(function() {
			$.ajax({
				data: $(this).serialize(),
				url: $(this).attr('action'),
				type: 'post',
				complete: function (rta) {
					rta_txt=rta.responseText;
					if (rta_txt.search('id="ir_a"') != -1) {
						fnjs_mostra_resposta(rta,'#main'); 
					} else {
						if (go) { 
							fnjs_update_div('#main',go);
						} else {
							alert ('no se donde ir');
						}
					}
				}
			});
			return false;
		});
		$(formulario).submit();
		$(formulario).off();
	}
}
</script>
<form id="frm_sin_nombre" name="frm_sin_nombre" action="" method="POST">
<?= $oHash->getCamposHtml(); ?>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?php echo ucfirst(_("cargo de una actividad")); ?></th></tr>
<tr><td class=etiqueta><?= ucfirst(_("asistente")) ?>:</td>
<?php
if (!empty($id_nom_real)) {
	echo "<td class=contenido>$ape_nom</td>";
} else {
	echo "<td>";
	echo $oDesplegablePersonas->desplegable();
	echo "</td>";
}
?>
</tr>
<tr><td class=etiqueta><?= ucfirst(_("cargo")) ?>:</td><td>
<?php echo $oDesplegableCargos->desplegable(); ?>
</td></tr>
<tr><td class=etiqueta><?= _("puede ser agd?") ?></td>
<td><input type="Checkbox" id="puede_agd" name="puede_agd" value="true" <?= $chk ?>></td></tr>
<tr><td class=etiqueta><?php echo ucfirst(_("observaciones")); ?></td><td>
<textarea class=contenido id="observ" name="observ" cols="40" rows="5"><?= htmlspecialchars($observ) ?></textarea></td></tr>
<?php
if ($_POST['mod']=="nuevo" && empty($id_nom_real)) {
	$asis_txt="<input type=\"Checkbox\" id=\"asis\" name=\"asis\" value=\"true\" checked>";
	echo "<tr><td class=etiqueta>"._("asiste?")."</td><td>$asis_txt</td></tr>";
}
?>	
</table>
<br><input type="button" id="guardar" name="guardar" onclick="fnjs_guardar(this.form);" value="<?php echo ucfirst(_("guardar datos del cargo")); ?>" align="MIDDLE">
</form>
