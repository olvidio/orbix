<?php
use actividadcargos\model as actividadcargos;
use actividades\model as actividades;
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
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
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

$obj = 'actividadcargos\\model\\ActividadCargo';

$permiso =	empty($_POST['permiso'])? '' : $_POST['permiso'];


if (!empty($id_activ)) { //caso de modificar
	$mod="editar";
	$oActividad = new actividades\Actividad(array('id_activ'=>$id_activ));
	$nom_activ=$oActividad->getNom_activ();
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla_dl = $oActividad->getId_tabla();
	$id_activ_real=$id_activ;
	$oActividadCargo=new actividadcargos\ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
	$puede_agd=$oActividadCargo->getPuede_agd();
	$observ=$oActividadCargo->getObserv();
} else { //caso de nuevo cargo
	$mod="nuevo";
	if (empty($_POST['id_tipo'])) {
		$mi_sfsv = core\ConfigGlobal::mi_sfsv();
		$id_tipo='^'.$mi_sfsv;  //caso genérico para todas las actividades
	} else {
		empty($_POST['id_tipo'])? $id_tipo="" : $id_tipo='^'.$_POST['id_tipo'];
	}
	if (!empty($_POST['que_dl'])) { 
		$aWhere['dl_org']=$_POST['que_dl'];
	} else {
		$aWhere['dl_org']=core\ConfigGlobal::mi_dele();
		$aOperadores['dl_org']='!=';
	}
	
	$aWhere['id_tipo_activ'] = $id_tipo;
	$aOperadores['id_tipo_activ']='~';
	$aWhere['status']=2;
	$aWhere['_ordre']='f_ini';

	$oGesActividades = new actividades\GestorActividad();
	$cActividades = $oGesActividades->getActividades($aWhere,$aOperadores); 

	$puede_agd="f"; //valor por defecto
	$observ=""; //valor por defecto
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
$camposNo = 'puede_agd';
$a_camposHidden = array(
		'id_nom' => $_POST['id_pau'],
		'mod'=> $_POST['mod'],
		'go_to' => $go_to
		);
		//'obj_pau'=> $obj_pau,
if (!empty($id_activ_real)) {
	$a_camposHidden['id_activ'] = $id_activ_real;
} else {
	if ($_POST['mod']=="nuevo") {
		$camposNo .= '!asis';
	}
	$camposForm .= '!id_activ';
}
$oHash->setCamposNo($camposNo);
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
<form id="frm_1302" name="frm_1302" action="apps/actividadcargos/controller/update_3102.php" method="POST">
<?= $oHash->getCamposHtml(); ?>
<input type="Hidden" id="mod" name="mod" value=<?= $mod ?>>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?php echo ucfirst(_("cargo de una actividad")); ?></th></tr>
<?php
if (!empty($id_activ_real)) {
	echo "<tr><td class=etiqueta>".ucfirst(_("actividad")).":</td><td class=contenido>$nom_activ</td>";
} else {
	echo "<tr><td class=etiqueta>".ucfirst(_("actividad")).":</td><td><select class=contenido id='id_activ' name='id_activ'>";
	$i=0;
	foreach ($cActividades as $oActividad) {
		$i++;
		$id_activ=$oActividad->getId_activ();
		$nom_activ=$oActividad->getNom_activ();
		//$id_activ==$id_pau ? $chk="selected": $chk=""; 
		echo "<option value=$id_activ>$nom_activ</option>";
	
	}
	echo "</select></td></tr>";
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
if ($_POST['mod']=="nuevo" && empty($id_activ_real)) {
	$asis_txt="<input type=\"Checkbox\" id=\"asis\" name=\"asis\" value=\"true\" checked>";
	echo "<tr><td class=etiqueta>"._("asiste?")."</td><td>$asis_txt</td></tr>";
}
?>	
</table>
<br><input type="button" id="guardar" name="guardar" onclick="fnjs_guardar(this.form);" value="<?php echo ucfirst(_("guardar datos del cargo")); ?>" align="MIDDLE">
</form>
