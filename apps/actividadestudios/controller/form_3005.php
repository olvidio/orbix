<?php
use actividades\model as actividades;
use asignaturas\model as asignaturas;
use actividadestudios\model as actividadestudios;
use profesores\model as profesores;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_asignatura=strtok($_POST['sel'][0],"#");
	$id_activ=strtok("#");
} else {
	empty($_POST['id_pau'])? $id_activ="" : $id_activ=$_POST['id_pau'];
}

if (!empty($_POST['go_to'])) {
	$go_to=urldecode($_POST['go_to']);
} else {
	empty($_POST['go_to'])? $go_to="" : $go_to=$_POST['go_to'];
}

$chk_a='';
$chk_c='';
$chk=''; 

$GesProfesores = new profesores\GestorProfesor();
$oDesplProfesores = $GesProfesores->getListaProfesores();
$oDesplProfesores->setNombre('id_profesor');
$oDesplProfesores->setBlanco('t');

if (!empty($id_asignatura)) { //caso de modificar
	$mod="editar"; 
	
	$oActividadAsignatura= new actividadestudios\ActividadAsignatura();
	$oActividadAsignatura->setId_activ($id_activ);
	$oActividadAsignatura->setId_asignatura($id_asignatura);
	$oActividadAsignatura->DBCarregar();

	$interes=$oActividadAsignatura->getInteres();
	$id_profesor=$oActividadAsignatura->getId_profesor();
	if (!empty($id_profesor)) {
		$oDesplProfesores->setOpcion_sel($id_profesor);
	}
	$aviso = $oActividadAsignatura->getAvis_profesor();
	$chk_a = ($aviso=="a")? "selected": '';
	$chk_c = ($aviso=="c")? "selected": '';
	$tipo=$oActividadAsignatura->getTipo();
	$chk = ($tipo=="p")? "selected": '';
	$f_ini = $oActividadAsignatura->getF_ini();
	$f_fin = $oActividadAsignatura->getF_fin();
		
	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto=$oAsignatura->getNombre_corto();
	$creditos=$oAsignatura->getCreditos();

	$primary_key_s="id_activ=$id_activ AND id_asignatura=$id_asignatura";
} else { //caso de nueva asignatura
	$mod="nuevo";
	$f_ini = '';
	$f_fin = '';
	$interes = 'f';
	if (!empty($_POST['id_pau'])) {
		$GesAsignaturas = new asignaturas\GestorAsignatura();
		$oDesplAsignaturas = $GesAsignaturas->getListaAsignaturas();
		$oDesplAsignaturas->setNombre('id_asignatura');
	} else {
		$go_to=urlencode(core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php?pau=${_POST['pau']}&id_pau=${_POST['id_pau']}&id_dossier=${_POST['id_dossier']}&tabla_pau=${_POST['tabla_pau']}&permiso=3");
		$oPosicion = new web\Posicion();
		echo $oPosicion->ir_a($go_to);
	}
}

$oHash = new web\Hash();
$camposForm = 'f_ini!f_fin!tipo!id_profesor';
$oHash->setCamposNo('mod!avis_profesor!interes');
$a_camposHidden = array(
		'id_activ' => $_POST['id_pau'],
		'go_to' => $go_to
		);
if (!empty($id_asignatura)) {
	$a_camposHidden['id_asignatura'] = $id_asignatura;
	$a_camposHidden['primary_key_s'] = $primary_key_s;
} else {
	$camposForm .= '!id_asignatura';
}
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<!-- ------------------- html -----------------------------------------------  -->
<script>
$(function() {
	$( "#f_ini" ).datepicker( {
		numberOfMonths: 3,
		showButtonPanel: true
		});

});
$(function() {
	$( "#f_fin" ).datepicker( {
		numberOfMonths: 3,
		showButtonPanel: true
		});

});
fnjs_guardar=function(){
	var err = 0;
	if ($('#frm_sin_nombre').value != undefined && !fnjs_comprobar_fecha('#f_ini')) { err=1; }
	if ($('#frm_sin_nombre').value != undefined && !fnjs_comprobar_fecha('#f_fin')) { err=1; }

	var rr=fnjs_comprobar_campos('#frm_sin_nombre','',0,'d_asignaturas_activ_dl');
	if (rr=='ok' && err==0) {
		$('#frm_sin_nombre').attr('action','apps/actividadestudios/controller/update_3005.php');
		fnjs_enviar_formulario('#frm_sin_nombre');
	}
}

</script>
<form id="frm_sin_nombre" name="frm_sin_nombre" action="" method="POST">
<?= $oHash->getCamposHtml(); ?>
<input type="Hidden" id="mod" name="mod" value=<?= $mod ?>>
<table>
<tr class=tab><th class=titulo_inv colspan=2><?= ucfirst(_("asignatura de una actividad")); ?></th></tr>
<?php
if (!empty($id_asignatura)) {
	echo "<tr><td class=etiqueta>".ucfirst(_("asignatura")).":</td><td class=contenido>$nombre_corto</td>";
} else {
	echo "<tr><td class=etiqueta>".ucfirst(_("asignatura")).":</td>
		<td>";
	echo $oDesplAsignaturas->desplegable();
	echo "</td></tr>";
}
//voy
echo "<tr><td class=etiqueta>".ucfirst(_("profesor")).":</td><td>";
echo $oDesplProfesores->desplegable();
?>
</td></tr>

<tr><td class=etiqueta><?= _("tipo") ?></td><td><select class=contenido id='tipo' name='tipo'>
<option></option>
<option value='p' <?= $chk ?> > <?= _("preceptor") ?></option>
</select></td></tr>

<tr><td class=etiqueta><?= _("profesor avisado?") ?></td><td><select class=contenido id='avis_profesor' name='avis_profesor'>
<option></option>
<option value='a' <?= $chk_a ?> ><?= _("avisado") ?></option>
<option value='c' <?= $chk_c ?> ><?=_("confirmado") ?></option>
</select></td></tr>
<tr><td class=etiqueta><?= _("inicio clases") ?></td><td><input id='f_ini' name='f_ini' type="text" class="fecha" size="11" value="<?= $f_ini ?>"></input></td></tr>
<tr><td class=etiqueta><?= _("fin clases") ?></td><td><input id='f_fin' name='f_fin' type="text" class="fecha" size="11" value="<?= $f_fin ?>"></input></td></tr>
<?php
$interes=="t" ? $chk="checked" : $chk="" ;
$chk_interes="<input type=\"Checkbox\" id=\"interes\" name=\"interes\" value=\"true\" $chk>";
echo "<tr><td class=etiqueta>"._("especial interés")."</td><td>$chk_interes</td></tr>";
?>
</table>
<br><input type="button" id="guardar" name="guardar" onclick="fnjs_guardar();" value="<?php echo ucfirst(_("guardar")); ?>" align="MIDDLE">
