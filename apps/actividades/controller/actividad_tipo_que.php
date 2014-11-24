<?php 
use usuarios\model as usuarios;
/**
* Esta página muestra un formulario para crear el tipo de actividad.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

$aSfsv=array(1=>'sv',2=>'sf');

if (empty($_POST['ssfsv'])) {
	$_POST['ssfsv']=$aSfsv[$miSfsv];
}
if (empty($_POST['status'])) $_POST['status']=2;
if (empty($_POST['sasistentes'])) $_POST['sasistentes']='';
if (empty($_POST['sactividad'])) $_POST['sactividad']='';
if (empty($_POST['snom_tipo'])) $_POST['snom_tipo']='';

if (!empty($id_tipo_activ))  {
	$oTipoActiv= new web\TiposActividades($id_tipo_activ);
	$_POST['ssfsv']=$oTipoActiv->getSfsvText();
	$_POST['sasistentes']=$oTipoActiv->getAsistentesText();
	$_POST['sactividad']=$oTipoActiv->getActividadText();
	$_POST['snom_tipo']=$oTipoActiv->getNom_tipoText();
} else {
	$oTipoActiv= new web\TiposActividades();
	// puede ser que tenga parte del id_tipo_activ.
	$_POST['ssfsv'] = empty($_POST['ssfsv'])? $miSfsv : $_POST['ssfsv'];
	if ($_POST['ssfsv']) $oTipoActiv->setSfsvText($_POST['ssfsv']);
	if ($_POST['sasistentes']) $oTipoActiv->setAsistentesText($_POST['sasistentes']);
	if ($_POST['sactividad']) $oTipoActiv->setActividadText($_POST['sactividad']);
}
$a_sfsv_posibles=$oTipoActiv->getSfsvPosibles();
$a_actividades_posibles=$oTipoActiv->getActividadesPosibles();
$a_nom_tipo_posibles=$oTipoActiv->getNom_tipoPosibles();


$array2=array();
if ($_SESSION['oPerm']->have_perm("est")) {
	$array_n = array(1=>'n', 3=>'agd');
	$array2 = array_merge($array2,$array_n);
}
if ($_SESSION['oPerm']->have_perm("sm")) {
	$array_n = array(1=>'n');
	$array2 = array_merge($array2,$array_n);
}
if ($_SESSION['oPerm']->have_perm("agd")) {
	$array_agd = array(3=>'agd');
	$array2 = array_merge($array2,$array_agd);
}
if ($_SESSION['oPerm']->have_perm("sg")) {
	$array_sg = array(4=>'s', 5=>'sg');
	$array2 = array_merge($array2,$array_sg);
}
if ($_SESSION['oPerm']->have_perm("des")) {
	if($_POST['status']==2) {
		$array_des = $oTipoActiv->getAsistentesPosibles(); //todos
	} else {
		$array_des = array(6=>'sss+');
	}
	$array2 = array_merge($array2,$array_des);
}
if ($_SESSION['oPerm']->have_perm("sr")) {
	$array_sr = array(7=>'sr');
	$array2 = array_merge($array2,$array_sr);
}

if ($_SESSION['oPerm']->have_perm("actividades")) { // des de la sf
	$array_des = $oTipoActiv->getAsistentesPosibles(); //todos
	$array2 = array_merge($array2,$array_des);
}


// si es una búsqueda, también puedo buscar todos. (Excepto sf/sv)
if (core\ConfigGlobal::is_jefeCalendario() || (isset($_POST['que']) && $_POST['que']=="buscar")) {
	$oTipoActivB= new web\TiposActividades();
	if ($_POST['ssfsv']) $oTipoActivB->setSfsvText($_POST['ssfsv']);
	$a_asistentes_posibles =$oTipoActivB->getAsistentesPosibles();
} else {
	//$array1=$oTipoActiv->getAsistentesPosibles();
	$oTipoActivB= new web\TiposActividades();
	if ($_POST['ssfsv']) $oTipoActivB->setSfsvText($_POST['ssfsv']);
	$array1=$oTipoActivB->getAsistentesPosibles();

	$a_asistentes_posibles = array_intersect($array1, $array2);
}

$oHashTipo = new web\Hash();
$oHashTipo->setUrl('apps/actividades/controller/actividad_tipo_get.php');
$oHashTipo->setCamposForm('salida!entrada');
$h = $oHashTipo->linkSinVal();

?>
<script>
fnjs_asistentes=function(){
	var isfsv=$('#isfsv_val').val();
	if (isfsv==3) {
		$('#iasistentes_val').hide();
		$('#iactividad_val').hide();
		fnjs_nom_tipo();
	} else {
		var url='<?= core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php' ?>';
		var parametros='salida=asistentes&entrada='+isfsv+'<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';

		$.ajax({
			data: parametros,
			url: url,
			type: 'post',
			dataType: 'html',
			complete: function (rta) {
				rta_txt=rta.responseText;
				$('#lst_asistentes').html(rta_txt);
			}
		});
		// borrar el resto.
		$('#iasistentes_val').val(".");
		$('#iactividad_val').val(".");
		$('#inom_tipo_val').val("...");
	}
}
fnjs_actividad=function(){
	var isfsv=$('#isfsv_val').val();
	var iasistentes=$('#iasistentes_val').val();
	var url='<?= core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php' ?>';
	var parametros='salida=actividad&entrada='+isfsv+iasistentes+'<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#lst_actividad').html(rta_txt);
		}
	});
}
fnjs_nom_tipo=function(){
	var isfsv=$('#isfsv_val').val();
	var iasistentes=$('#iasistentes_val').val();
	var iactividad=$('#iactividad_val').val();
	var url='<?= core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php' ?>';
	var parametros='salida=nom_tipo&entrada='+isfsv+iasistentes+iactividad+'<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#lst_nom_tipo').html(rta_txt);
		}
	});
}
fnjs_act_id_activ=function(){
	var que=$('#que').val();
	var isfsv=$('#isfsv_val').val();
	var iasistentes=$('#iasistentes_val').val();
	var iactividad=$('#iactividad_val').val();
	var inom_tipo=$('#inom_tipo_val').val();
	var id_tipo_activ=isfsv+iasistentes+iactividad+inom_tipo;

	$('#id_tipo_activ').val(id_tipo_activ);
	if (que == 'proceso') {
		fnjs_ver();
	} else {
		// buscar la tarifa para este tipo de actividad
		var url='<?= core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php' ?>';
		var parametros='salida=tarifa&entrada='+id_tipo_activ+'=<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
		$.ajax({
			url: url,
			type: 'post',
			data: parametros,
			success: function (rta) {
					rta_txt=rta.responseText;
					//alert ('respuesta: '+rta_txt);
					$('#tarifa').val(rta_txt);			
				}
		});
	}
	if ( que=="cambiar_tipo" && confirm ("<?= _("¿Quiere cambiar el nombre de la actividad?") ?>") ) {
		fnjs_generarNomActiv('#modifica');
	}
}
</script>
<table><tr>
<?php
if (core\ConfigGlobal::is_jefeCalendario()
	   	or (($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd")) && $miSfsv == 1)
  		or (!empty($pag_usuarios) && $pag_usuarios=='perm')
   ) { 
	?>
	<td class="etiqueta"><?= _("sf/sv") ?></td>
	<td><select id="isfsv_val" name="isfsv_val" class="contenido" style="width: 4em;" onchange="fnjs_asistentes()" >
		<?php
		echo "<option value=\".\"></option>";
		foreach ($a_sfsv_posibles as $clave=>$val) {
			if ($_POST['ssfsv']==$val) {
				$sel_sfsv="selected";
			} else { $sel_sfsv=""; }
			echo "<option value=\"$clave\" $sel_sfsv>$val</option>";
		 }
		?>
	</select></td>
	<?php
} else {
	?>
	<input type="hidden" id="isfsv_val" name="isfsv_val" value="<?= $oTipoActiv->getSfsvId(); ?>" >
	<?php
}
?>
<td class="etiqueta"><?= _("asistentes") ?></td>
	<td id="lst_asistentes">
	<select id="iasistentes_val" name="iasistentes_val" class="contenido" onchange="fnjs_actividad();">
	<?php
	echo "<option value=\".\"></option>";
	foreach ($a_asistentes_posibles as $clave=>$val) {
		if ($_POST['sasistentes']==$val) {
			$sel_asis="selected";
		} else { $sel_asis=""; }
		echo "<option value=\"$clave\" $sel_asis>$val</option>";
	}
	?>
	</select>
	</td>
<td class="etiqueta"><?= _("actividad") ?></td>
	<td id="lst_actividad">
	<select id="iactividad_val" name="iactividad_val" class="contenido" onchange="fnjs_nom_tipo();">
	<?php
	echo "<option value=\".\"></option>";
	foreach ($a_actividades_posibles as $clave=>$val) {
		if ($_POST['sactividad']==$val) {
			$sel_act="selected";
		} else { $sel_act=""; }
		echo "<option value=\"$clave\" $sel_act>$val</option>";
	}
	?>
	</select>
	</td>
<td class="etiqueta"><?= _("tipo actividad") ?></td>
	<td id="lst_nom_tipo">
	<select id="inom_tipo_val" name="inom_tipo_val" class="contenido" onchange="fnjs_act_id_activ();">
	<?php
		echo "<option value=\"...\"></option>";
		foreach ($a_nom_tipo_posibles as $clave=>$val) {
			if ($_POST['snom_tipo']==$val) {
				$sel_tip="selected";
			} else { $sel_tip=""; }
			echo "<option value=\"$clave\" $sel_tip>$val</option>";
		}
	?>
	</td>
</tr>
</table>
