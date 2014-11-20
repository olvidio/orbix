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
	$id_asignatura=strtok($_POST['sel'][0],"#");
	empty($_POST['id_pau'])? $id_activ="" : $id_activ=$_POST['id_pau'];
} else {
	empty($_POST['id_asignatura'])? $id_asignatura="" : $id_asignatura=$_POST['id_asignatura'];
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
}

$GesNotas = new notas\GestorNota();
$cNotas = $GesNotas->getNotas();
$aNotas = array();
foreach ($cNotas as $oNota) {
	$id = $oNota->getId_situacion();
	$descripcion = $oNota->getDescripcion();
	$aNotas[$id] = $descripcion;
}
$oDesplNotas = new web\Desplegable();
$oDesplNotas->setNombre("id_situacion[]");
$oDesplNotas->setOpciones($aNotas);
$oDesplNotas->setBlanco(true);
$oDesplNotas->setAction("fnjs_guardar()");

		//db_desplegable("id_situacion[]",$x_situacion,$id_situacion,0);
$sql_situacion="SELECT id_situacion, descripcion FROM e_notas_situacion";
$oDBSt_x_situacion=$oDB->query($sql_situacion);

$oActividad = new actividades\Actividad($id_activ);
$nom_activ = $oActividad->getNom_activ();

$GesMatriculas = new actividadestudios\GestorMatricula();
$cMatriculados = $GesMatriculas->getMatriculas(array('id_asignatura'=>$id_asignatura, 'id_activ'=>$id_activ));
$matriculados=count($cMatriculados);
// para ordenar
$aPersonasMatriculadas = array(); 
foreach($cMatriculados as $oMatricula) {
	
	$id_nom=$oMatricula->getId_nom();
	$oPersona = personas\Persona::NewPersona($id_nom);
	$nom=$oPersona->getApellidosNombre();
	$aPersonasMatriculadas[$nom] = $oMatricula;
}
uksort($aPersonasMatriculadas, "strsinacentocmp"); // compara sin contar los acentos i insensitive.

$_POST['que'] = empty($_POST['que'])? '' : $_POST['que'];
$_POST['id_pau'] = empty($_POST['id_pau'])? '' : $_POST['id_pau'];
$_POST['opcional'] = empty($_POST['opcional'])? '' : $_POST['opcional'];
$_POST['go_to'] = empty($_POST['go_to'])? '' : $_POST['go_to'];
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
include_once ("acta_ver.php"); 
?>
<script>
fnjs_guardar_todo=function(){
	$('#que').val("3");
	$('#f_1303').attr('action',"est/acta_notas_update.php");
	fnjs_enviar_formulario('#f_1303');
}
fnjs_guardar=function(){
	$('#que').val("1");
	$('#f_1303').attr('action',"est/acta_notas_update.php");
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
<table>
<thead><tr><th class=titulo_inv colspan=4><?= strtoupper(_("notas del acta")); ?></th></tr>
<tr><th><?= _("alumno"); ?></th><th><?= _("preceptor"); ?></th><th><?= _("nota"); ?></th></tr>
</thead>
<tbody>
<input type="Hidden" id="que" name="que" value="<?= $_POST['que'] ?>">
<input type="Hidden" id="id_pau" name="id_pau" value="<?= $_POST['id_pau'] ?>">
<input type="Hidden" id="id_activ" name="id_activ" value="<?= $id_activ ?>">
<input type="Hidden" id="opcional" name="opcional" value="<?= $_POST['opcional'] ?>">
<input type="Hidden" id="go_to" name="go_to" value="<?= $_POST['go_to'] ?>">
<input type="Hidden" id="primary_key_s" name="primary_key_s" value="<?= $_POST['primary_key_s'] ?>">
<input type="Hidden" id="id_asignatura" name="id_asignatura" value="<?= $id_asignatura ?>">
<input type="Hidden" id="id_nivel" name="id_nivel" value="<?= $_POST['id_nivel'] ?>">
<input type="Hidden" id="matriculados" name="matriculados" value="<?= $matriculados ?>">
<?php
$i=0;
foreach ($aPersonasMatriculadas as $key=>$oMatricula) {
	$i++;
	
	$nom=$key;
	$id_nom=$oMatricula->getId_nom();
	$id_situacion=$oMatricula->getId_situacion();
	$preceptor=$oMatricula->getPreceptor();

	echo "<input type=\"Hidden\" id=\"id_nom[]\" name=\"id_nom[]\" value=\"$id_nom\">";
	echo "<tr><td>$nom</td>";
	if ($preceptor=="t") { $chk_tipo="selected"; } else { $chk_tipo=""; }
	echo "<td><select id=\"form_preceptor[]\" name=\"form_preceptor[]\" onchange=\"javascript:fnjs_guardar()\">
			<option />
			<option value=\"p\" $chk_tipo>"._("preceptor")."</option>
		</select>
		</td>";
	echo "<td class=contenido>";
	$oDesplNotas->setOpcion_sel($id_situacion);
	echo $oDesplNotas->desplegable();
	echo "</td></tr>";
}
?>	
</tbody></table>
</form>
<br><input type="button" value="<?php echo strtoupper(_("grabar e imprimir")); ?>" onclick="fnjs_guardar_todo()">
