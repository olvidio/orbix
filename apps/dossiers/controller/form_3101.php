<?php
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

if (!empty($id_nom)) { //caso de modificar
	$mod="editar";
	$oPersona = personas\Persona::NewPersona($id_nom);
	$ape_nom = $oPersona->getApellidosNombre();
	$id_tabla = $oPersona->getId_tabla();
	$id_nom_real = $id_nom;

	$oAsistente=new asistentes\Asistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
	$propio=$oAsistente->getPropio();
	$falta=$oAsistente->getFalta();
	$est_ok=$oAsistente->getEst_ok();
	$observ=$oAsistente->getObserv();
} else { //caso de nuevo asistente
	$mod="nuevo";
	$propio="t"; //valor por defecto
	$observ=""; //valor por defecto
	$_POST['tabla_p'] = !empty($_POST['tabla_p'])? $_POST['tabla_p'] : '';
	switch ($_POST['tabla_p']) {
		case 'p_numerarios':
		case 'p_agregados':
		case 'p_supernumerarios':
		case 'p_sssc':
		case 'p_nax':
			$oPersonas=new personas\GestorPersonaDl();
			$oPersonasOpciones=$oPersonas->getListaPersonasTabla($_POST['tabla_p'],'');
			$oDesplegablePersonas=new web\Desplegable('id_nom',$oPersonasOpciones,'',false);
			break;
		case '':
		default:
			empty($_POST['na'])? $tipo="" : $tipo="p".$_POST['na'];
			$oPersonas=new personas\GestorPersonaEx();
			$oPersonasOpciones=$oPersonas->getListaPersonasTabla($_POST['tabla_p'],$tipo);
			$oDesplegablePersonas=new web\Desplegable('id_nom',$oPersonasOpciones,'',false);
			break;
		/*
		   default:
			$go_to=urlencode(core\ConfigGlobal::getWeb()."/programas/dossiers/dossiers_ver.php?pau=$pau&id_pau=${_POST['id_pau']}&id_dossier=$id_dossier&tabla_pau=$tabla_pau&permiso=3");
			include_once(core\ConfigGlobal::$dir_programas.'/func_web.php');  
			$r=ir_a($go_to);
			*/
	}
}
(!empty($propio) && $propio=="t") ? $propio_chk="checked" : $propio_chk="" ;
(!empty($falta) && $falta=="t") ? $falta_chk="checked" : $falta_chk="" ;
(!empty($est_ok) && $est_ok=="t") ? $est_chk="checked" : $est_chk="" ;

?>
<!-- ------------------- html -----------------------------------------------  -->
<script>
fnjs_guardar=function(formulario){
	var rr=fnjs_comprobar_campos(formulario,'<?= addslashes($obj) ?>');
	//alert ("EEE "+rr);
	if (rr=='ok') {
		go=$('#go_to').val();
		$(formulario).attr('action',"apps/dossiers/controller/update_3101.php");
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
<input type="Hidden" id="id_activ" name="id_activ" value="<?= $_POST['id_pau'] ?>">
<input type="Hidden" id="go_to" name="go_to" value="<?= $go_to ?>">
<input type="Hidden" id="mod" name="mod" value=<?= $mod ?>>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?php echo ucfirst(_("Asistente a una actividad")); ?></th></tr>
<tr><td class=etiqueta><?= ucfirst(_("asistente")) ?>:</td>
<?php
if (!empty($id_nom_real)) {
	echo "<input type=\"Hidden\" id=\"id_nom\" name=\"id_nom\" value=$id_nom_real>";
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
