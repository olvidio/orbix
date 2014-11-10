<?php
/**
 * Muestra un formulario para introducir/cambiar los datos del objeto ActividadCargo
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
 * @param boolean $_POST['puede_agd'] 
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("classes/personas/personas_gestor.class");
	require_once ("classes/activ-personas/d_cargos_activ.class");
	require_once ("classes/personas/xd_orden_cargo_gestor.class");
	require_once ("classes/web/desplegable.class");

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_cargo=strtok("#");
} else {
	$id_nom="";
	$id_cargo="";
}
$id_activ=$_POST['id_pau'];

if (!empty($_POST['go_to'])) {
	$go_to=urldecode($_POST['go_to']);
} else {
	empty($_POST['go_to'])? $go_to="" : $go_to=$_POST['go_to'];
}

if (!empty($id_nom)) { //caso de modificar
	$oPersona=new Persona($id_nom);
	$ape_nom=$oPersona->getApellidosNombre();
	$id_tabla=$oPersona->getId_tabla();
	$id_nom_real=$id_nom;
	$oActividadCargo=new ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
	$puede_agd=$oActividadCargo->getPuede_agd();
	$observ=$oActividadCargo->getObserv();
} else { //caso de nuevo cargo
	$observ="";
	if (!empty($_POST['tabla_p'])) {
		empty($_POST['na'])? $tipo="" : $tipo="p".$_POST['na'];
		$oPersonas=new GestorPersona();
		$oPersonasOpciones=$oPersonas->getListaPersonasTabla($_POST['tabla_p'],$tipo);
		$oDesplegablePersonas=new Desplegable('id_nom',$oPersonasOpciones,'',false);
	} else {
		$go_to=urlencode(ConfigGlobal::$web."/programas/dossiers/dossiers_ver.php?pau=$pau&id_pau=$id_pau&id_dossier=$id_dossier&tabla_pau=$tabla_pau&permiso=3");
		/**
		* Funciones que agilizan la navegación web
		*/
		include_once("../func_web.php");  
		$r=ir_a($go_to);
	}
}
$oCargos=new GestorCargo();
$aOpciones=$oCargos->getCargosActividades();
$oDesplegableCargos=new Desplegable('id_cargo',$aOpciones,$id_cargo,false);
(!empty($puede_agd) && $puede_agd=="t") ? $chk="checked" : $chk="" ;
?>
<!-- ------------------- html -----------------------------------------------  -->
<script>
fnjs_guardar=function(formulario){
	var rr=fnjs_comprobar_campos(formulario,'d_cargos_activ','no');
	//alert ("EEE "+rr);
	if (rr=='ok') {
		go=$('#go_to').val();
		$(formulario).attr('action',"programas/dossiers/update_3102.php");
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
<input type="Hidden" id="id_activ" name="id_activ" value="<?= $_POST['id_pau'] ?>">
<input type="Hidden" id="go_to" name="go_to" value="<?= $go_to ?>">
<input type="Hidden" id="mod" name="mod" value=<?= $_POST['mod'] ?>>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?php echo ucfirst(_("cargo de una actividad")); ?></th></tr>
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
