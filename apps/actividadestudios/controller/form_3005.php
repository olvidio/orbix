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

$obj = 'actividadestudios\\model\\ActividadAsignatura';

$go_to = (string)  \filter_input(INPUT_POST, 'go_to');
if (!empty($go_to)) {
	$go_to=urldecode($go_to);
}


$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$id_activ = strtok($a_sel[0],"#");
	$id_asignatura=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,0);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,0);
	if (!empty($go_to)) {
		// add stack:
		$stack = $oPosicion->getStack();
		$go_to .= "&stack=$stack";
	}
} else {
	$id_asignatura = empty($_POST['id_asignatura'])? "" : $_POST['id_asignatura'];
	$id_activ = empty($_POST['id_pau'])? "" : $_POST['id_pau'];
}

$chk_a='';
$chk_c='';
$chk=''; 

if (!empty($id_asignatura)) { //caso de modificar
	$mod="editar"; 
	$GesProfesores = new profesores\GestorProfesor();
	$oDesplProfesores = $GesProfesores->getDesplProfesoresAsignatura($id_asignatura);
	$oDesplProfesores->setOpcion_sel(-1);
	
	$oActividadAsignatura= new actividadestudios\ActividadAsignaturaDl();
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
	$GesProfesores = new profesores\GestorProfesorActividad();
	$oDesplProfesores = $GesProfesores->getListaProfesoresActividad(array($id_activ));
	$oDesplProfesores->setOpcion_sel(-1);
	
	$f_ini = '';
	$f_fin = '';
	$interes = 'f';
	if (!empty($id_activ)) {
		$GesAsignaturas = new asignaturas\GestorAsignatura();
		$oDesplAsignaturas = $GesAsignaturas->getListaAsignaturas(false);
		$oDesplAsignaturas->setNombre('id_asignatura');
		$oDesplAsignaturas->setAction("fnjs_mas_profes('asignatura')");
	} else {
		$id_dossier = (integer)  \filter_input(INPUT_POST, 'id_dossier');
		$tabla_pau = (string)  \filter_input(INPUT_POST, 'tabla_pau');
		$go_to=urlencode(core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php?pau=a&id_pau=$id_activ&id_dossier=$id_dossier&tabla_pau=$tabla_pau&permiso=3");
		$oPosicion2 = new web\Posicion();
		echo $oPosicion2->ir_a($go_to);
	}
}

$oDesplProfesores->setNombre('id_profesor');
$oDesplProfesores->setBlanco('t');

$oHash = new web\Hash();
$camposForm = 'f_ini!f_fin!tipo!id_profesor';
$oHash->setCamposNo('mod!avis_profesor!interes');
$a_camposHidden = array(
		'id_activ' => $id_activ,
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


$oHashTipo = new web\Hash();
$oHashTipo->setUrl('apps/actividadestudios/controller/lista_profesores.php');
$oHashTipo->setCamposForm('salida');
$h = $oHashTipo->linkSinVal();

$oHashTipo->setCamposForm('salida!id_activ');
$h1 = $oHashTipo->linkSinVal();

$oHashTipo->setCamposForm('salida!id_activ!id_asignatura');
$h2 = $oHashTipo->linkSinVal();

echo $oPosicion->mostrar_left_slide();
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
fnjs_mas_profes=function(filtro){
	var url='<?= core\ConfigGlobal::getWeb().'/apps/actividadestudios/controller/lista_profesores.php' ?>';
	switch (filtro) {
		case 'asignatura':
			id_asignatura = $("#id_asignatura").val();
			var parametros='salida=asignatura&id_asignatura='+id_asignatura+'&id_activ=<?= $id_activ ?><?= $h2 ?>&PHPSESSID=<?php echo session_id(); ?>';
			break;
		case 'dl':
			var parametros='salida=dl&id_activ=<?= $id_activ ?><?= $h1 ?>&PHPSESSID=<?php echo session_id(); ?>';
			break;
		case 'all':
			var parametros='salida=todos<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
			break;
	}
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#lst_profes').html(rta_txt);
		}
	});
}

fnjs_guardar=function(){
	var err = 0;
	if ($('#frm_sin_nombre').value != undefined && !fnjs_comprobar_fecha('#f_ini')) { err=1; }
	if ($('#frm_sin_nombre').value != undefined && !fnjs_comprobar_fecha('#f_fin')) { err=1; }

	var rr=fnjs_comprobar_campos('#frm_sin_nombre','<?= addslashes($obj) ?>');

	if (rr=='ok' && err==0) {
		$('#frm_sin_nombre').attr('action','apps/actividadestudios/controller/update_3005.php');
		fnjs_enviar_formulario('#frm_sin_nombre');
	}
}

</script>
<form id="frm_sin_nombre" name="frm_sin_nombre" action="" method="POST">
<?= $oHash->getCamposHtml(); ?>
<input type="Hidden" id="mod" name="mod" value="<?= $mod ?>" >
<table>
<tr class=tab><th class=titulo_inv colspan=5><?= ucfirst(_("asignatura de una actividad")); ?></th></tr>
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
?>
<tr><td class=etiqueta><?= ucfirst(_("profesor")) ?>:</td>
<td id="lst_profes">
<?= $oDesplProfesores->desplegable(); ?>
</td>
<td><input type=button onclick="fnjs_mas_profes('asignatura')" value="<?= _('corresponde') ?>"></td>
<td><input type=button onclick="fnjs_mas_profes('dl')" value="<?= _('dl y asistentes') ?>"></td>
<td><input type=button onclick="fnjs_mas_profes('all')" value="<?= _('otros de paso') ?>"></td>
</tr>

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
