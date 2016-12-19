<?php
use actividades\model as actividades;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
    $id_activ=strtok($id_sel[0],"#");
    $nom_activ=strtok("#");
	//if (empty($nom_activ) && !empty($id_activ)) {
	//	$nom_activ = $oActividad->getNom_activ();
	//}
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
} else { // vengo de actualizar
	$id_activ = (integer)  filter_input(INPUT_POST, 'id_activ');
	$nom_activ = (string)  filter_input(INPUT_POST, 'nom_activ');
	
	/*
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
	 * 
	 */
}


$gesAsistentes = new asistentes\model\GestorAsistente();
$oActividad = new actividades\Actividad($id_activ);

$queSel = empty($_POST['queSel'])? '' : $_POST['queSel'];

	
// Seleccionar los id_dl del mismo grupo de estudios
$esquema = core\ConfigGlobal::mi_region();
$a_reg = explode('-',$esquema);
$mi_dl = substr($a_reg[1],0,-1); // quito la v o la f.
$aWhere =array('region'=>$a_reg[0],'dl'=>$mi_dl);
$oMiDelegacion = new ubis\model\Delegacion($aWhere);
$grupo_estudios = $oMiDelegacion->getGrupo_estudios();

$gesDelegacion = new ubis\model\GestorDelegacion();
$cDelegaciones = $gesDelegacion->getDelegaciones(array('_ordre'=>'region,dl'));
// array de id=>dl
foreach ($cDelegaciones as $oDelegacion) {
	$dl = $oDelegacion->getDl();
	$id_dl = $oDelegacion->getId_dl();
	$a_dele[$id_dl] = $dl;
	$a_id_dele[$dl] = $id_dl;
}
$oDesplDelegaciones = $gesDelegacion->getListaDelegaciones(array('H'));
$oDesplDelegaciones->setNombre('dl');

$gesActividadPlazas = new \actividadplazas\model\GestorActividadPlazas();

$id_tipo_activ = $oActividad->getId_tipo_activ();
$id_activ = $oActividad->getId_activ();
$nom = $oActividad->getNom_activ();
$dl_org = $oActividad->getDl_org();
$plazas_totales = $oActividad->getPlazas();
if (empty($plazas_totales)) {
	$id_ubi = $oActividad->getId_ubi();
	$oCasa = ubis\model\Ubi::NewUbi($id_ubi);
	$plazas_totales = $oCasa->getPlazas();
	if (empty($plazas_totales)) {
		$plazas_totales = '?';
	}
}
// plazas de calendario de cada dl
$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>$id_activ));
foreach ($cActividadPlazas as $oActividadPlazas) {
	$id_dl = $oActividadPlazas->getId_dl();
	$dl_tabla = $oActividadPlazas->getDl_tabla();
	if ($dl_org == $dl_tabla) {
		$a_plazas[$id_dl]['calendario'] = $oActividadPlazas->getPlazas();
		// las cedidas se guardan en la tabla que pertenece a la dl
		if($id_dl === $a_id_dele[$dl_org]) {
			$json_cedidas = $oActividadPlazas->getCedidas();
			if (!empty($json_cedidas)){
				$aCedidas = json_decode($json_cedidas,TRUE);
				$a_plazas[$id_dl]['cedidas'] = $aCedidas;
			} else {
				$a_plazas[$id_dl]['cedidas'] = array();
			}
		}
	} else { //para plazas cedidas de una dl que no es la que organiza.
		$json_cedidas = $oActividadPlazas->getCedidas();
		if (!empty($json_cedidas)){
			$aCedidas = json_decode($json_cedidas,TRUE);
			$a_plazas[$id_dl]['cedidas'] = $aCedidas;
		} else {
			$a_plazas[$id_dl]['cedidas'] = array();
		}
	}
	$a_plazas[$id_dl]['conseguidas'] = array();
	$a_plazas[$id_dl]['total_cedidas'] = 0;
	$a_plazas[$id_dl]['total_conseguidas'] = 0;
}
//Calcular totales
$tot_calendario = 0;
$tot_actual = 0;
$tot_ocupadas = 0;
foreach ($a_plazas as $id_dl=>$aa) {
	$total_cedidas = 0;
	$num_plazas_calendario = $aa['calendario'];
	$aCedidas = $aa['cedidas'];
	$dl = $a_dele[$id_dl];
	foreach ($aCedidas as $dl_otra=>$num_plazas){
		$id_dl_otra = $a_id_dele[$dl_otra];
		if ($id_dl != $id_dl_otra && array_key_exists($id_dl_otra,$a_plazas)) {
			$a_plazas[$id_dl_otra]['conseguidas'][$dl] = $num_plazas;
		} else {
			$tot_actual += $num_plazas;
		} 
		$total_cedidas += $num_plazas;
	}
	$a_plazas[$id_dl]['total_cedidas'] = $total_cedidas;
	$tot_calendario += $num_plazas_calendario;
}
foreach ($a_plazas as $id_dl=>$aa) {
	$total_conseguidas = 0;
	$dl = $a_dele[$id_dl];
	$aCedidas = $aa['conseguidas'];
	foreach ($aCedidas as $dl_otra=>$num_plazas){
		$total_conseguidas += $num_plazas;
	}
	$a_plazas[$id_dl]['total_conseguidas'] = $total_conseguidas;
}
foreach ($a_plazas as $id_dl=>$aa) {
	$dl = $a_dele[$id_dl];
	$pl_calendario = $aa['calendario'];
	$pl_cedidas = $aa['total_cedidas'];
	$pl_conseguidas = $aa['total_conseguidas'];

	$pl_actual = $pl_calendario - $pl_cedidas + $pl_conseguidas;
	
	$a_plazas[$id_dl]['total_actual'] = $pl_actual;
	$tot_actual += $pl_actual;

	$dl = $a_dele[$id_dl];
	$ocupadas = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dl);
	if ($ocupadas < 0) { // No se sabe
		$a_plazas[$id_dl]['ocupadas'] = '?';
	} else {
		$a_plazas[$id_dl]['ocupadas'] = $ocupadas;
	}
	$tot_ocupadas += $ocupadas;
}



$oHash = new web\Hash();
$camposForm = 'num_plazas!dl';
$a_camposHidden = array(
		'id_activ' => $id_activ,
		'que' => 'ceder'
		);
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new web\Hash();
$a_camposHidden1 = array(
		'id_activ' => $id_activ,
		'nom_activ' => $nom_activ
		);
$oHash1->setArraycamposHidden($a_camposHidden1);

/* ---------------------------------- html --------------------------------------- */
echo $oPosicion->atras();
?>
<script>
fnjs_guardar=function(formulario){
	$('#que').value = 'ceder';
	$(formulario).attr('action',"apps/actividadplazas/controller/resumen_plazas_update.php");
	$(formulario).submit(function() {
		$.ajax({
			data: $(this).serialize(),
			url: $(this).attr('action'),
			type: 'post',
			complete: function (rta) { 
				rta_txt=rta.responseText;
				if (rta_txt != '' && rta_txt != '\n') {
					alert (rta_txt);
				}
			},
			success: function() { fnjs_actualizar() }
		});
		return false;
	});
	$(formulario).submit();
	$(formulario).off();
}

fnjs_actualizar=function(){
	$('#frm_actualizar').attr('action','apps/actividadplazas/controller/resumen_plazas.php');
	fnjs_enviar_formulario('#frm_actualizar');
}
</script>
<form id='frm_actualizar'>
	<?= $oHash1->getCamposHtml(); ?>
</form>
<form id="frm_sin_nombre" name="frm_sin_nombre" action="" method="POST">
<?= $oHash->getCamposHtml(); ?>
<?= $nom_activ ?>
<table border="1">
	<tr><td>dl</td><td colspan="4">plazas</td><td>ocupadas</td><td>libres</td></tr>
	<tr><td></td><td>calendario</td><td>cedidas</td><td>conseguidas</td><td>total</td><td></td><td></td></tr>
	<?php
	//plazas
	$d = 0;
	foreach ($a_plazas as $id_dl=>$pl) {
		$d++;
		$clase = "tono$d";
		echo "<tr class='$clase'>";
		echo "<td>".$a_dele[$id_dl]."</td><td>".$pl['calendario']."</td>";
		echo "<td>".$pl['total_cedidas']."</td>";
		echo "<td>".$pl['total_conseguidas']."</td>";
		echo "<td>".$pl['total_actual']."</td>";
		echo "<td>".$pl['ocupadas']."</td>";
		echo "</tr>";
		if (!empty($pl['cedidas'])){
			$aCedidas = $pl['cedidas'];
			foreach ($aCedidas as $dl=>$num_plazas){
				$id_dl_otra = $a_id_dele[$dl];	
				echo "<tr class='$clase'><td></td><td></td><td>$num_plazas a $dl</td>";
				if (!array_key_exists($id_dl_otra,$a_plazas)) {
					echo "<td></td><td>$num_plazas</td><td></td></tr>";
				}
				echo "</tr>";
			}
		}
		if (!empty($pl['conseguidas'])){
			$aCedidas = $pl['conseguidas'];
			foreach ($aCedidas as $dl=>$num_plazas){
				echo "<tr class='$clase'><td></td><td></td><td></td><td>$num_plazas de $dl</td></tr>";
			}
		}
	}
	// TOTALES
	echo "<tr>";
	echo "<td>"._("totales")."</td><td>$tot_calendario ($plazas_totales)</td>";
	echo "<td>".$total_cedidas."</td>";
	echo "<td>".$total_conseguidas."</td>";
	echo "<td>".$tot_actual."</td>";
	echo "<td>".$tot_ocupadas."</td>";
	echo "</tr>";

	
	?>
</table>

<br><br>
<form id="ceder">
ceder 
<input name="num_plazas" type="text" size="3" />
plazas a 
<?= $oDesplDelegaciones->desplegable() ?>
<input type="button" id="ok" name="ok" onclick="fnjs_guardar(this.form);" value="<?php echo ucfirst(_("guardar")); ?>" align="MIDDLE" />

</form>