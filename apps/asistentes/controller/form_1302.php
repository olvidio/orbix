<?php
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
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("classes/actividades/ext_a_actividades_gestor.class");
	require_once ("classes/activ-personas/d_cargos_activ.class");
	require_once ("classes/activ-personas/d_asistentes_activ.class");
//	require_once ("classes/personas/xd_orden_cargo_gestor.class");
	require_once ("classes/web/desplegable.class");

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_activ=strtok($_POST['sel'][0],"#");
	$id_cargo=strtok("#");
} else {
	$id_activ="";
	$id_cargo="";
}
$id_nom=$_POST['id_pau'];

if (!empty($_POST['go_to'])) {
	$go_to=urldecode($_POST['go_to']);
} else {
	empty($_POST['go_to'])? $go_to="" : $go_to=$_POST['go_to'];
}


if (!empty($id_activ)) { //caso de modificar
	$id_activ_real=$id_activ;
	$oActividad=new Actividad($id_activ);
	$nom_activ=$oActividad->getNom_activ();
	$oActividadCargo=new ActividadCargo(array('id_tipo_activ'=>$id_activ,'id_cargo'=>$id_cargo));
	$puede_agd=$oActividadCargo->getPuede_agd();
	$observ=$oActividadCargo->getObserv();
	//asistencia
	$oActividadAsistente=new ActividadAsistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
	if ($oActividadAsistente->DBCarregar('guardar') === false) { //no existe
		$chk_asis="";
	} else {
		$chk_asis="checked";
	}
} else { //caso de nuevo cargo
	$observ="";
	$chk_asis="checked"; //por defecto si asiste.
	if (empty($_POST['id_tipo'])) { 
		$id_tipo="1.....";  //caso genérico para todas las actividades
	} else {
		$id_tipo=$_POST['id_tipo'];
	}
	if ($id_tipo{0}==2) {	 //activ de la sf
		$chk_asis="";	//por defecto no asiste.
		$id_cargo=35;	//por defecto cargo=sacd.
	}
	
	if (!empty($_POST['que_dl'])) {
		$mis="AND dl_org='".$_POST['que_dl']."'";
	} else {
		$mis="AND dl_org!='".ConfigGlobal::$dele."'";
	}
	$condicion="AND status=2 $mis";

	$oActividades=new GestorActividad();
	$oActividadesOpciones=$oActividades->getListaActividadesDeTipo($id_tipo,$condicion);
	$oDesplegableActividades=new Desplegable('id_activ',$oActividadesOpciones,'',false);
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
				complete: function (rta_txt) {
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

<form id="frm_1302" name="frm_1302" action="" method="POST">
<input type="Hidden" id="id_nom" name="id_nom" value="<?= $_POST['id_pau'] ?>">
<input type="Hidden" id="go_to" name="go_to" value="<?= $_POST['go_to'] ?>">
<input type="Hidden" id="mod" name="mod" value=<?= $_POST['mod'] ?>>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?= ucfirst(_("cargo de una actividad")); ?></th></tr>
<tr><td class=etiqueta><?= ucfirst(_("actividad")) ?>:</td>
<?php
if (!empty($id_activ_real)) {
	echo "<input type=\"Hidden\" id=\"id_activ\" name=\"id_activ\" value=$id_activ_real>";
	echo "<td><b>$nom_activ</b></td>";
} else {
	echo "<td>";
	echo $oDesplegableActividades->desplegable();
	echo "</td>";
}
?>
<tr><td class=etiqueta><?= ucfirst(_("cargo")) ?>:</td><td>
<?php echo $oDesplegableCargos->desplegable(); ?>
</td></tr>
<tr><td class=etiqueta><?= _("puede ser agd?") ?></td>
<td><input type="Checkbox" id="puede_agd" name="puede_agd" value="true" <?= $chk ?>></td></tr>
<tr><td class=etiqueta><?php echo ucfirst(_("observaciones")); ?></td><td>
<textarea class=contenido id="observ" name="observ" cols="40" rows="5"><?= htmlspecialchars($observ) ?></textarea></td></tr>
<tr><td><?= _("asiste?") ?></td><td>
<input type="Checkbox" id="asis" name="asis" value="true" <?= $chk_asis ?> >
</td></tr>
</table>
<br><input type="button" id="guardar" name="guardar" onclick="fnjs_guardar(this.form);" value="<?php echo ucfirst(_("guardar datos del cargo")); ?>" align="MIDDLE">
</form>
