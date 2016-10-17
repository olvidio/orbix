<?php
use actividades\model as actividades;
use asistentes\model as asistentes;
use personas\model as personas;
/**
 * Muestra un formulario para introducir/cambiar los datos del objeto Asistente
 * Si se crea un nuevo cargo, machaca el anteriro (si lo hubiere)
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
 * @param boolean $_POST['propio'] 
 * @param boolean $_POST['falta'] 
 * @param boolean $_POST['puede_agd'] 
 * @param string $_POST['observ'] optional
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_nom=strtok($_POST['sel'][0],"#");
} else {
	$id_nom="";
}
$id_activ=$_POST['id_pau'];

if (!empty($_POST['go_to'])) {
	$go_to=urldecode($_POST['go_to']);
} else {
	empty($_POST['go_to'])? $go_to="" : $go_to=$_POST['go_to'];
}

$obj = 'asistentes\\model\\Asistente';
$permiso =	empty($_POST['permiso'])? '' : $_POST['permiso'];

/* Mirar si la actividad es mia o no */
$oActividad = new actividades\Actividad($id_activ);
// si es de la sf quito la 'f'
$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
$id_tabla_dl = $oActividad->getId_tabla();

if (!empty($id_nom)) { //caso de modificar
	$mod="editar";
	$oPersona = personas\Persona::NewPersona($id_nom);
	$ape_nom = $oPersona->getApellidosNombre();
	$id_tabla = $oPersona->getId_tabla();
	$id_nom_real = $id_nom;

	$obj_pau = str_replace("personas\\model\\",'',get_class($oPersona));
	if ($dl == core\ConfigGlobal::mi_dele()) {
		switch ($obj_pau) {
			case 'PersonaN':
			case 'PersonaNax':
			case 'PersonaAgd':
			case 'PersonaS':
			case 'PersonaSSSC':
			case 'PersonaDl':
				$oAsistente=new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			case 'PersonaOut':
				$oAsistente=new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			case 'PersonaIn':
				// Supongo que sólo debería modificar la dl origen.
				// $oAsistente=new asistentes\AsistenteIn(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				exit (_("Los datos de asistencia los modifica la dl del asistente"));
				break;
			case 'PersonaEx':
				$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
		}
	} else {
		if ($id_tabla_dl == 'dl') { 
				$oAsistente=new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		} else {
			$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		}
	}
	$propio=$oAsistente->getPropio();
	$falta=$oAsistente->getFalta();
	$est_ok=$oAsistente->getEst_ok();
	$observ=$oAsistente->getObserv();

} else { //caso de nuevo asistente
	$mod="nuevo";
	$propio="t"; //valor por defecto
	$observ=""; //valor por defecto
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
			$oPersonas=new personas\GestorPersonaSSSC();
			$oDesplegablePersonas = $oPersonas->getListaPersonas();
			$oDesplegablePersonas->setNombre('id_nom');
			break;
		case 'PersonaEx':
			$oPersonas=new personas\GestorPersonaEx();
			$oDesplegablePersonas = $oPersonas->getListaPersonas($na_val);
			$oDesplegablePersonas->setNombre('id_nom');
			$obj_pau = 'PersonaEx';
			break;
	}
}
$propio_chk = (!empty($propio) && $propio=='t') ? 'checked' : '' ;
$falta_chk = (!empty($falta) && $falta=='t') ? 'checked' : '' ;
$est_chk = (!empty($est_ok) && $est_ok=='t') ? 'checked' : '' ;


$oHash = new web\Hash();
$camposForm = 'observ';
$oHash->setCamposNo('mod!propio!falta!est_ok');
$a_camposHidden = array(
		'id_activ' => $_POST['id_pau'],
		'obj_pau'=> $obj_pau,
		'go_to' => $go_to
		);
if (!empty($id_nom_real)) {
	$a_camposHidden['id_nom'] = $id_nom_real;
} else {
	$camposForm .= '!id_nom';
}
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<!-- ------------------- html -----------------------------------------------  -->
<script>
fnjs_guardar=function(formulario){
	var rr=fnjs_comprobar_campos(formulario,'<?= addslashes($obj) ?>');
	//alert ("EEE "+rr);
	if (rr=='ok') {
		go=$('#go_to').val();
		$(formulario).attr('action',"apps/asistentes/controller/update_3101.php");
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
						if (go) fnjs_update_div('#main',go); 
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
<input type="Hidden" id="mod" name="mod" value=<?= $mod ?>>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?php echo ucfirst(_("Asistente a una actividad")); ?></th></tr>
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
<tr><td class=etiqueta><?= _("propio") ?></td>
<td><input type="Checkbox" id="propio" name="propio" value="true" <?= $propio_chk ?>></td></tr>
<tr><td class=etiqueta><?= _("falta") ?></td>
<td><input type="Checkbox" id="falta" name="falta" value="true" <?= $falta_chk ?>></td></tr>
<tr><td class=etiqueta><?= _("estudios confirmados") ?></td>
<td><input type="Checkbox" id="est_ok" name="est_ok" value="true" <?= $est_chk ?>></td></tr>
<tr><td class=etiqueta><?php echo ucfirst(_("observaciones")); ?></td><td class=contenido>
<textarea id="observ" name="observ" cols="40" rows="5"><?= htmlspecialchars($observ) ?></textarea></td></tr>
</table>
<br><input type="button" id="guardar" name="guardar" onclick="fnjs_guardar(this.form);" value="<?php echo ucfirst(_("guardar datos del asistente")); ?>" align="MIDDLE">
</form>
