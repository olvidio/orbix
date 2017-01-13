<?php
use actividades\model as actividades;
use actividadestudios\model as actividadestudios;
use notas\model as notas;
use personas\model as personas;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//include_once(core\ConfigGlobal::$dir_programas.'/func_web.php');

$notas=1; // para indicar a la página de actas que está dentro de ésta.
if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_activ = strtok($_POST['sel'][0],"#");
	$id_asignatura=strtok("#");
} else {
	empty($_POST['id_asignatura'])? $id_asignatura="" : $id_asignatura=$_POST['id_asignatura'];
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
}


$GesNotas = new notas\GestorNota();
$oDesplNotas = $GesNotas->getListaNotas();
$oDesplNotas->setNombre('id_situacion[]');


$oActividad = new actividades\Actividad($id_activ);
$nom_activ = $oActividad->getNom_activ();

$GesMatriculas = new actividadestudios\GestorMatricula();
$cMatriculados = $GesMatriculas->getMatriculas(array('id_asignatura'=>$id_asignatura, 'id_activ'=>$id_activ));
$matriculados=count($cMatriculados);
if ($matriculados > 0) {
	// para ordenar
	$msg_err = '';
	$aPersonasMatriculadas = array(); 
	foreach($cMatriculados as $oMatricula) {
		$id_nom=$oMatricula->getId_nom();
		$oPersona = personas\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom";
			continue;
		}
		$nom=$oPersona->getApellidosNombre();
		$aPersonasMatriculadas[$nom] = $oMatricula;
	}
	uksort($aPersonasMatriculadas, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
} else {
	echo _("No hay ninguna persona matriculada de esta asignatura");
}

$_POST['que'] = empty($_POST['que'])? '' : $_POST['que'];
$_POST['id_pau'] = empty($_POST['id_pau'])? '' : $_POST['id_pau'];
$_POST['opcional'] = empty($_POST['opcional'])? '' : $_POST['opcional'];
//$_POST['go_to'] = empty($_POST['go_to'])? '' : $_POST['go_to'];
$_POST['go_to'] = 'acta_notas';
$_POST['primary_key_s'] = empty($_POST['primary_key_s'])? '' : $_POST['primary_key_s'];
$_POST['id_nivel'] = empty($_POST['id_nivel'])? '' : $_POST['id_nivel'];

$GesActas = new notas\GestorActa();
$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
if (is_array($cActas) && count($cActas) == 1) {
	$oActa = $cActas[0];
	$acta=$oActa->getActa();
	$notas="acta"; // para indicar a la página de actas que está dentro de ésta.
} else {
	$notas="nuevo";// para indicar a la página de actas que está dentro de ésta.
}
include_once ("apps/notas/controller/acta_ver.php"); 


$oHash1 = new web\Hash();
$oHash1->setcamposForm('id_nom!nota_num!nota_max!form_preceptor');
$oHash1->setCamposNo('que');
$a_camposHidden1 = array(
		'id_pau' => $_POST['id_pau'],
		'id_activ' => $id_activ,
		'opcional' => $_POST['opcional'],
		'go_to' => $_POST['go_to'],
		'primary_key_s' => $_POST['primary_key_s'],
		'id_asignatura' => $id_asignatura,
		'id_nivel' => $_POST['id_nivel'],
		'matriculados' => $matriculados
		);
$oHash1->setArraycamposHidden($a_camposHidden1);

if (!empty($msg_err)) { echo $msg_err; }
?>
<script>
fnjs_nota=function(n){
	var num;
	var max;
	var sit;
	
	num = $('#nota_num'+n).val();
	max = $('#nota_max'+n).val();
/*	sit = $('#id_situacion').val();
	if (!num)  $('#id_situacion').val('0');
	num = parseFloat(num);
	if (typeof num == 'number' && num > 1) {
 		$('#id_situacion').val(10);
	}
	*/
	max_default = <?= core\ConfigGlobal::nota_max(); ?>;
	if (!max)  $('#nota_max'+n).val(max_default);
	fnjs_guardar();
}

fnjs_guardar_todo=function(){
	$('#que').val("3");
	$('#f_1303').attr('action',"apps/actividadestudios/controller/acta_notas_update.php");
	fnjs_enviar_formulario('#f_1303');
}

fnjs_guardar=function(){
	$('#que').val("1");
	$('#f_1303').attr('action',"apps/actividadestudios/controller/acta_notas_update.php");
	$('#f_1303').submit(function() {
		$.ajax({
			data: $(this).serialize(),
			url: $(this).attr('action'),
			type: 'post',
			complete: function (rta) {
				rta_txt=rta.responseText;
				if (rta_txt != '' && rta_txt != '\n') {
					alert (rta_txt);
				}
			}
		});
		return false;
	});
	$('#f_1303').submit();
	$('#f_1303').off();
}
</script>
<form id="f_1303" name="f_1303" action="" method="POST">
<?= $oHash1->getCamposHtml(); ?>
<input type="Hidden" id="que" name="que" value="<?= $_POST['que'] ?>">
<table>
<thead><tr><th class=titulo_inv colspan=5><?= strtoupper(_("notas del acta")); ?></th></tr>
<tr><th><?= _("alumno"); ?></th><th><?= _("preceptor"); ?></th><th colspan=3><?= _("nota"); ?></th></tr>
</thead>
<tbody>
<?php
if ($matriculados > 0) {
	$i=0;
	foreach ($aPersonasMatriculadas as $key=>$oMatricula) {
		$i++;
		
		$nom=$key;
		$id_nom=$oMatricula->getId_nom();
		$nota_num=$oMatricula->getNota_num();
		$nota_max=$oMatricula->getNota_max();
		$id_situacion=$oMatricula->getId_situacion();
		$preceptor=$oMatricula->getPreceptor();

		$oDesplNotas->setOpcion_sel($id_situacion);

		echo "<input type=\"Hidden\" id=\"id_nom[]\" name=\"id_nom[]\" value=\"$id_nom\">";
		echo "<tr><td>$nom</td>";
		if ($preceptor=="t") { $chk_tipo="selected"; } else { $chk_tipo=""; }
		echo "<td><select id=\"form_preceptor[]\" name=\"form_preceptor[]\" onchange=\"javascript:fnjs_guardar()\">
				<option />
				<option value=\"p\" $chk_tipo>"._("preceptor")."</option>
			</select>
			</td>";
		echo "<td class=contenido>";

		echo "<input type=\"text\" id=\"nota_num$i\" name=\"nota_num[]\" value=\"$nota_num\" size=2 onchange='fnjs_nota($i)'>";
		echo ' ' . _("sobre") . ' ';
		echo "<input type=\"text\" id=\"nota_max$i\" name=\"nota_max[]\" value=\"$nota_max\" size=2>";
		//echo "<td>"._("situación")."</td><td>";
		//echo $oDesplNotas->desplegable();

		echo "</td></tr>";
	}
}
?>	
</tbody></table>
</form>
<br><input type="button" value="<?php echo strtoupper(_("grabar e imprimir")); ?>" onclick="fnjs_guardar_todo()">
