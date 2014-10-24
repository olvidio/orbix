<?php 
use actividades\model as actividades;
use ubis\model as ubis;

/**
* Esta página muestra un formulario para crear una nueva actividad.
*
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$miSfsv=core\ConfigGlobal::mi_sfsv();

$a_status = array( 1 => _("proyecto"), 2 => _("actual"), 3 => _("terminada"), 4 => _("borrable"));

if (empty($_POST['dl_org'])) { 
	/* Pienso que si cada dl crea las suyas, no hace falta esto.
	if ($_SESSION['oPerm']->have_perm("est")) {
		$dl_org = ''; 
	} else {
		$dl_org = core\ConfigGlobal::mi_dele(); 
	}
	*/
	$dl_org = core\ConfigGlobal::mi_dele(); 
} else {
	$dl_org = $_POST['dl_org'];
}
// si es nueva, obligatorio estado: proyecto (14.X.2011)
$status = empty($_POST['status'])? 1 : $_POST['status'];

$mod = empty($_POST['mod'])? '' : $_POST['mod'];
$que = empty($_POST['que'])? '' : $_POST['que'];
$id_activ = empty($_POST['id_activ'])? '' : $_POST['id_activ'];
$id_tipo_activ = empty($_POST['id_tipo_activ'])? '' : $_POST['id_tipo_activ'];
$nom_activ = empty($_POST['nom_activ'])? '' : $_POST['nom_activ'];
$id_ubi = empty($_POST['id_ubi'])? '' : $_POST['id_ubi'];
$desc_activ = empty($_POST['desc_activ'])? '' : $_POST['desc_activ'];
$f_ini = empty($_POST['f_ini'])? '' : $_POST['f_ini'];
$h_ini = empty($_POST['h_ini'])? '' : $_POST['h_ini'];
$f_fin = empty($_POST['f_fin'])? '' : $_POST['f_fin'];
$h_fin = empty($_POST['h_fin'])? '' : $_POST['h_fin'];
$precio = empty($_POST['precio'])? '' : $_POST['precio'];
$num_asistentes = empty($_POST['num_asistentes'])? '' : $_POST['num_asistentes'];
$observ = empty($_POST['observ'])? '' : $_POST['observ'];
$nivel_stgr = empty($_POST['nivel_stgr'])? '' : $_POST['nivel_stgr'];
$id_repeticion = empty($_POST['id_repeticion'])? '' : $_POST['id_repeticion'];
$observ_material = empty($_POST['observ_material'])? '' : $_POST['observ_material'];
$lugar_esp = empty($_POST['lugar_esp'])? '' : $_POST['lugar_esp'];
$tarifa = empty($_POST['tarifa'])? '' : $_POST['tarifa'];

$nombre_ubi=_("sin determinar");
if ($id_ubi != 0) {
	$oUbi = new Ubi($id_ubi);
	$nombre_ubi=$oUbi->getNombre_ubi();
} else {
	if ($id_ubi==0 && $lugar_esp) $nombre_ubi=$lugar_esp;
	if (!$id_ubi && !$lugar_esp) $nombre_ubi=_("sin determinar");
}
/*
if ($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd")) {
	$Bdl="t";
} else {
	$Bdl="f";
}
*/
$Bdl="t";

$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones($Bdl);
$oDesplDelegacionesOrg->setNombre('dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($dl_org);

$oGesTipoTarifa = new actividades\GestorTipoTarifa();
$oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($miSfsv);
$oDesplPosiblesTipoTarifas->setNombre('tarifa');
$oDesplPosiblesTipoTarifas->setOpcion_sel($tarifa);

$oGesNivelStgr = new actividades\GestorNivelStgr();
$oDesplNivelStgr = $oGesNivelStgr->getListaNivelesStgr();
$oDesplNivelStgr->setNombre('nivel_stgr');
$oDesplNivelStgr->setOpcion_sel($nivel_stgr);

$oGesRepeticion = new actividades\GestorRepeticion();
$oDesplRepeticion = $oGesRepeticion->getListaRepeticion();
$oDesplRepeticion->setNombre('id_repeticion');
$oDesplRepeticion->setOpcion_sel($id_repeticion);


$oHash = new web\Hash();
$camposForm = 'status!id_tipo_activ!mod!dl_org!f_fin!f_ini!h_fin!h_ini!id_repeticion!id_ubi!lugar_esp!nom_activ!nombre_ubi!observ!precio!tarifa!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val';
if ($_SESSION['oPerm']->have_perm("est")) {
	$camposForm .= '!nivel_stgr';
}
$oHash->setcamposForm($camposForm);
//$oHash->setCamposNo('mod!que');
$a_camposHidden = array(
		'que' => $que,
		'id_activ' => $id_activ
		);
$oHash->setArraycamposHidden($a_camposHidden);


$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv'); 
$h = $oHash1->linkSinVal();

?>
<script>
$(function() { $( "#f_ini" ).datepicker(); });
$(function() { $( "#f_fin" ).datepicker(); });
fnjs_cambiar_ubi=function(){
	var dl_org=$('#dl_org').val();
	var isfsv_val=$('#isfsv_val').val();
	var ssfsv="";
	switch(isfsv_val){
		case "1": ssfsv="sv"; break;
		case "2": ssfsv="sf"; break;
		case "3": ssfsv="reservada"; break;
	}
	var array_org=dl_org.split('#');
	//alert("algo:"+sfsv+" " + array_org[0]);
	var winPrefs="dependent=yes,width=950,height=700,screenX=200,screenY=200,titlebar=yes,scrollbars=yes";
	top.newWin = window.open("<?= core\ConfigGlobal::getWeb() ?>/apps/actividades/controller/actividad_select_ubi.php?dl_org="+array_org[0]+"&ssfsv="+ssfsv+"<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>", "sele", winPrefs);
	top.newWin.focus();

}
fnjs_guardar=function(tipo){
	var perm_crear = <?php if ($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd")) { echo 1; } else { echo 0; } ?>;
	var err=0;
	var id_tipo_activ=$('#id_tipo_activ').val();
	var nom_activ=$('#nom_activ').val();
	if (!fnjs_comprobar_fecha('#f_ini')) { err=1; }
	if (!fnjs_comprobar_fecha('#f_fin')) { err=1; }
	if (!fnjs_comprobar_hora('#h_ini')) { err=1; }
	if (!fnjs_comprobar_hora('#h_fin')) { err=1; }
	var dl_org=$('#dl_org').val();
	var estado = $('#status').val();

	/* comprobar si el id_tipo_actividad está completo */
	var id_sfsv_val=$('#isfsv_val').val();
	var id_asistentes_val=$('#iasistentes_val').val();
	var id_actividad_val=$('#iactividad_val').val();
	var id_nom_tipo_val=$('#inom_tipo_val').val();

	if (id_sfsv_val == '.') { alert("<?= _("Debe concretar la sección en el tipo de Actividad") ?>"); err=1; }
	if (id_asistentes_val == '.') { alert("<?= _("Debe concretar los asistentes en el tipo de Actividad") ?>"); err=1; }
	if (id_actividad_val == '.') { alert("<?= _("Debe concretar la actividad en el tipo de Actividad") ?>"); err=1; }
	if (id_nom_tipo_val == '...') { alert("<?= _("Debe concretar el tipo de Actividad") ?>"); err=1; }
	/* fin de tipo */

	if (id_tipo_activ == 0) { alert("<?= _("Debe concretar el tipo de Actividad") ?>"); err=1; }
	if (nom_activ == '') { alert("<?= _("Debe llenar el campo del Nombre de Actividad") ?>"); err=1; }
	if (nom_activ.length > 69 ) { alert("<?= _("El campo del Nombre de Actividad es demasiado largo") ?>"); err=1; }
	if (!f_ini.value) { alert("<?= _("Debe llenar el campo de fecha inicio") ?>"); err=1; }
	if (!f_fin.value) { alert("<?= _("Debe llenar el campo de fecha fin") ?>"); err=1; }
	if (!dl_org) { 
		alert("<?= _("Debe llenar el campo de Organiza") ?>"); err=1;
	} else {
		if (dl_org == '<?= core\ConfigGlobal::mi_dele() ?>' && estado != 1 && perm_crear != 1) {
			alert("<?= _("No tiene permiso para crear la actividad en estado actual. La actividad se guardará en proyecto") ?>");
			$('#status_1').checked=true;
		}
	}
	var rr=fnjs_comprobar_campos('#modifica','',0,'a_actividades');
	//alert ("EEE "+rr);
	if (rr=='ok' && err==0) {
		$('#mod').val(tipo);
		$('#modifica').attr('action','apps/actividades/controller/actividad_update.php');
		fnjs_enviar_formulario('#modifica');
	}
}

</script>
</head>
<body>
<form id="modifica" name="modifica" action="" method="post">
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='id_tipo_activ' name='id_tipo_activ' value='<?= $id_tipo_activ ?>'>
<input type='hidden' id='mod' name='mod' value='<?= $mod ?>'>
<table><tr><th class=titulo_inv><?php echo ucfirst(_("nueva actividad")); ?>
</th></tr>
<tr><td class=subtitulo><?php echo ucfirst(_("escoger el tipo de actividad")); ?>
</td></tr>
<tr>
<?php include ("actividad_tipo_que.php") ?>
</tr></table>
<br>
  	<table>
	<tr>
	<td class=etiqueta><?php echo ucfirst(_("estado")); ?>*:</td>
	<?php if (!core\ConfigGlobal::is_app_installed('procesos')) { ?>
		<td><input type="radio" id="status_1" name="status" value="1" <?php if ($status==1) { echo "checked";} ?>><?php echo _("proyecto"); ?></td>
		<td><input type="radio" id="status_2" name="status" value="2" <?php if ($status==2) { echo "checked";} ?>><?php echo _("actual"); ?></td>
		<td><input type="radio" id="status_3" name="status" value="3" <?php if ($status==3) { echo "checked";} ?>><?php echo _("terminada"); ?></td>
		<td><input type="radio" id="status_4" name="status" value="4" <?php if ($status==4) { echo "checked";} ?>><?php echo _("borrable"); ?></td>
	<?php } else { ?>
		<!-- Ara faig que no es pugui canviar. S'ha d'anar per les fases.  -->
		<td><?= $a_status[$status] ?></td>
		<input type='hidden' id='status' name='status' value='<?= $status ?>'>
	<?php } ?>
	</tr>
	<td class=etiqueta><?php echo _("organiza"); ?>:</td>
	<td colspan=3>
	<?php echo $oDesplDelegacionesOrg->desplegable(); ?>
	</td>
	</tr>
	  <td class=etiqueta><?php echo ucfirst(_("nombre actividad")); ?>*:</td><td colspan=7>
	  <input class=contenido size='50' id='nom_activ' name='nom_activ' value='<?= $nom_activ ?>'>&nbsp;&nbsp;&nbsp;<span class=link onclick=fnjs_generarNomActiv('#modifica')>Generar</span>
</td></tr>
<tr>
	  <td class=etiqueta><?php echo ucfirst(_("fecha inicio")); ?>*:</td><td><input class=fecha size="10" id="f_ini" name="f_ini" value="<?= $f_ini ?>">
	  <td class=etiqueta><?php echo ucfirst(_("hora inicio")); ?>:</td><td><input class=contenido size="10" id="h_ini" name="h_ini" value="<?= $h_ini ?>" title="<?= _('formato [hh:mm]') ?>">
</td></tr>
  <tr>
	  <td class=etiqueta><?php echo ucfirst(_("fecha fin")); ?>*:</td><td><input class=fecha size="10" id="f_fin" name="f_fin" value="<?= $f_fin ?>">
	  <td class=etiqueta><?php echo ucfirst(_("hora fin")); ?>:</td><td><input class=contenido size="10" id="h_fin" name="h_fin" value="<?= $h_fin ?>" title="<?= _('formato [hh:mm]') ?>">
</td></tr>
<tr>
<tr>
	  <td class=etiqueta>
	    <?php echo ucfirst(_("lugar")); ?>: 
	 </td><td colspan=2 class=contenido ><span class="link" onclick="fnjs_cambiar_ubi();" id="span_nom_ubi"><?= $nombre_ubi ?></span>
	<input type=hidden id="nombre_ubi" name="nombre_ubi" value="<?= $nombre_ubi ?>">
	 <input type=hidden id="id_ubi" name="id_ubi" value="<?= $id_ubi ?>">
	 <input type=hidden id="lugar_esp" name="lugar_esp" value="<?= $lugar_esp ?>">
	 </td>
</tr>
<tr>
<td class=etiqueta><?php echo ucfirst(_("tarifa")); ?>: </td>
  <td><?php echo $oDesplPosiblesTipoTarifas->desplegable(); ?></td>
<td class=etiqueta><?php echo ucfirst(_("precio")); ?>: </td>
  <td>
  <input type=text class="contenido derecha" id="precio" name="precio" value='<?= $precio ?>' size="8" onblur="fnjs_comprobar_dinero('#precio');"> <?= _('€') ?>
</td></tr>
<tr><td class=etiqueta><?php echo ucfirst(_("observaciones")); ?>:</td><td colspan=5><input class=contenido size="30" id="observ" name="observ" value="<?= htmlspecialchars($observ) ?>">
</td></tr>

<tr>
<td class=etiqueta><?php echo ucfirst(_("repetición anual por")); ?>:</td>
<td colspan=2>
<?php echo $oDesplRepeticion->desplegable(); ?>
</td>
<?php if ($_SESSION['oPerm']->have_perm("est")) { ?>
<td class=etiqueta><?php echo ucfirst(_(" nivel de stgr")); ?>:</td>
<td colspan=2>
<?php echo $oDesplNivelStgr->desplegable(); ?>
</td>
<?php } ?>
</tr>
</table>
<?php
if ($que == 'cambiar_tipo') {
	echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("guardar ficha"))."\" onclick=\"javascript:fnjs_guardar('cmb_tipo')\"> ";
} else {
	echo "<input TYPE=\"button\" VALUE=\"".ucfirst(_("crear ficha"))."\" onclick=\"javascript:fnjs_guardar('nuevo')\"> ";
}
?>
<input TYPE="reset" VALUE="<?php echo ucfirst(_('borrar')); ?>">
</form>
<?php 
