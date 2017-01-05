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
}

$gesDelegacion = new ubis\model\GestorDelegacion();
$oDesplDelegaciones = $gesDelegacion->getListaDelegaciones(array('H'));
$oDesplDelegaciones->setNombre('dl');

/*
$cDelegaciones = $gesDelegacion->getDelegaciones(array('_ordre'=>'region,dl'));
// array de id=>dl
foreach ($cDelegaciones as $oDelegacion) {
	$dl = $oDelegacion->getDl();
	$id_dl = $oDelegacion->getId_dl();
	$a_dele[$id_dl] = $dl;
	$a_id_dele[$dl] = $id_dl;
}
*/

$gesActividadPlazas = new \actividadplazas\model\GestorResumenPlazas();
$gesActividadPlazas->setId_activ($id_activ);
$a_plazas = $gesActividadPlazas->getResumen();

$plazas_totales = $a_plazas['total']['actividad'];
$tot_calendario = $a_plazas['total']['calendario'];
$tot_cedidas = $a_plazas['total']['cedidas'];
$tot_conseguidas = $a_plazas['total']['conseguidas'];
$tot_actual = $a_plazas['total']['actual'];
$tot_ocupadas = $a_plazas['total']['ocupadas'];



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
	foreach ($a_plazas as $dl=>$pl) {
		if ($dl == 'total') { continue; }
		$d++;
		$clase = "tono$d";
		echo "<tr class='$clase'>";
		echo "<td>".$dl."</td><td>".$pl['calendario']."</td>";
		echo "<td>".$pl['total_cedidas']."</td>";
		echo "<td>".$pl['total_conseguidas']."</td>";
		echo "<td>".$pl['total_actual']."</td>";
		echo "<td>".$pl['ocupadas']."</td>";
		echo "<td></td>";
		echo "</tr>";
		if (!empty($pl['cedidas'])){
			$aCedidas = $pl['cedidas'];
			foreach ($aCedidas as $dl_otra=>$num_plazas){
				echo "<tr class='$clase'><td></td><td></td><td>$num_plazas a $dl_otra</td>";
				if (!array_key_exists($dl_otra,$a_plazas)) {
					echo "<td></td><td>$num_plazas</td>";
					$ocupadas = empty($a_plazas[$dl][$dl_otra]['ocupadas'])? 0 : $a_plazas[$dl][$dl_otra]['ocupadas'];
					echo "<td>$ocupadas</td><td></td></tr>";
				} else {
					echo "<td></td><td></td>";
					echo "<td></td><td></td></tr>";
				}
				echo "</tr>";
			}
		}
		if (!empty($pl['conseguidas'])){
			$aCedidas = $pl['conseguidas'];
			foreach ($aCedidas as $dl_otra=>$num_plazas){
				echo "<tr class='$clase'><td></td><td></td><td></td><td>$num_plazas de $dl_otra</td>";
				echo "<td></td><td></td><td></td></tr>";
			}
		} else {
			echo "<tr class='$clase'><td></td><td></td><td></td><td></td>";
			echo "<td></td><td></td><td></td></tr>";
		}
	}
	// TOTALES
	echo "<tr>";
	echo "<td>"._("totales")."</td><td>$tot_calendario ($plazas_totales)</td>";
	echo "<td>".$tot_cedidas."</td>";
	echo "<td>".$tot_conseguidas."</td>";
	echo "<td>".$tot_actual."</td>";
	echo "<td>".$tot_ocupadas."</td>";
	echo "<td></td>";
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