<?php 
use actividades\model as actividades;
use ubis\model as ubis;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************


if (isset($_POST['sel'])) { //vengo de un checkbox
	//$id_nom=$sel[0];
	$id_activ=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
} else {
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
	empty($_POST['tabla'])? $tabla="" : $tabla=$_POST['tabla'];
}

$_POST['tipo'] = isset($_POST['tipo']) ? $_POST['tipo'] : '';
$_POST['tabla'] = isset($_POST['tabla']) ? $_POST['tabla'] : '';

$godossiers = web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>'a','id_pau'=>$id_activ,'tabla_pau'=>$_POST['tabla'])));

$a_status = array( 1 => _("proyecto"), 2 => _("actual"), 3 => _("terminada"), 4 => _("borrable"));

$oActividad = new actividades\Actividad($id_activ);
extract($oActividad->getTot());
// mirar permisos.
//if(core\ConfigGlobal::is_app_installed('procesos')) {
	$_SESSION['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
	$oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

	if ($oPermActiv->only_perm('ocupado')) exit();
//}

$oTipoActiv= new web\TiposActividades($id_tipo_activ);
$ssfsv=$oTipoActiv->getSfsvText();
$sasistentes=$oTipoActiv->getAsistentesText();
$sactividad=$oTipoActiv->getActividadText();
$snom_tipo=$oTipoActiv->getNom_tipoText();


if (!empty($id_ubi) && $id_ubi != 1) {
	$oCasa = ubis\Ubi::newUbi($id_ubi);
	$nombre_ubi=$oCasa->getNombre_ubi();
	$delegacion=$oCasa->getDl();
	$region=$oCasa->getRegion();
	$sv=$oCasa->getSv();
	$sf=$oCasa->getSf();
} else {
	if ($id_ubi==1 && $lugar_esp) $nombre_ubi=$lugar_esp;
	if (!$id_ubi && !$lugar_esp) $nombre_ubi=_("sin determinar");
}

// Para incluir o no la dl (core\ConfigGlobal::mi_dele()).
$Bdl="t";
if(core\ConfigGlobal::is_app_installed('procesos')) {
	if ($oPermActiv->have_perm('ver')) {
		$Bdl="t";
	} else {
		$Bdl="f";
	}
}
$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones($Bdl);
$oDesplDelegacionesOrg->setNombre('dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($dl_org);

$oGesTipoTarifa = new actividades\GestorTipoTarifa();
$isfsv=$oTipoActiv->getSfsvId();
$oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($isfsv);
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

$alt=_("ver dossiers");
$dos=_("dossiers");


$oHash = new web\Hash();
$oHash->setcamposForm('status!dl_org!f_fin!f_ini!h_fin!h_ini!id_repeticion!id_ubi!lugar_esp!mod!nivel_stgr!nom_activ!nombre_ubi!observ!precio!que!sactividad!sasistentes!snom_tipo!tarifa!publicado');
$oHash->setCamposNo('mod!que');
$a_camposHidden = array(
		'id_tipo_activ' => $id_tipo_activ,
		'id_activ' => $id_activ,
		'ssfsv' => $ssfsv
		);
$oHash->setArraycamposHidden($a_camposHidden);


$oHash1 = new web\Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv'); 
$h = $oHash1->linkSinVal();

echo $oPosicion->atras();
// ----------------------- cabecera ------------------------
?>
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
fnjs_cambiar_ubi=function(){
	var dl_org=$('#dl_org').val();
	var ssfsv=$('#ssfsv').val();
	var array_org=dl_org.split('#');
	//alert("algo:" + array_org[0]);
	var winPrefs="dependent=yes,width=950,height=700,screenX=200,screenY=200,titlebar=yes,scrollbars=yes";
	top.newWin = window.open("<?= core\ConfigGlobal::getWeb() ?>/apps/actividades/controller/actividad_select_ubi.php?dl_org="+array_org[0]+"&ssfsv="+ssfsv+"<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>", "sele", winPrefs);
	top.newWin.focus();
}
fnjs_guardar=function(){
	var err = 0;
	if (!fnjs_comprobar_fecha('#f_ini')) { err=1; }
	if (!fnjs_comprobar_fecha('#f_fin')) { err=1; }
	if (!fnjs_comprobar_hora('#h_ini')) { err=1; }
	if (!fnjs_comprobar_hora('#h_fin')) { err=1; }

	var rr=fnjs_comprobar_campos('#modifica','',0,'a_actividades');
	//alert ("EEE "+rr);
	if (rr=='ok' && err==0) {
		$('#mod').val('editar');
		$('#que').val('actualizar');
		$('#modifica').attr('action','apps/actividades/controller/actividad_update.php');
		fnjs_enviar_formulario('#modifica');
	}
}

fnjs_actualizar_sacd=function(){
	$('#modifica').attr('action','programas/actividad_update.php');
	$('#mod').val('actualizar_sacd');
	fnjs_enviar_formulario('#modifica');
}
fnjs_actualizar_ctr=function(){
	$('#modifica').attr('action','programas/actividad_update.php');
	$('#mod').val('actualizar_ctr');
	fnjs_enviar_formulario('#modifica');
}

fnjs_tipo_actividad=function(){
	$('#modifica').attr('action','programas/actividad_nueva.php');
	$('#que').val('cambiar_tipo');
	fnjs_enviar_formulario('#modifica');
}
</script>
<!-- -----------------------------  cabecera --------------------------------- -->
<table><tr>
<td>
<span class=link onclick="fnjs_update_div('#main','<?= $godossiers ?>')" ><img src=<?= core\ConfigGlobal::$web_icons ?>/dossiers.gif border=0 width=40 height=40 alt='<?= $alt ?>'>(<?= $dos ?>)</span>
</td>
<td class=titulo><?= $nom_activ ?></td>
</table>
<form id="modifica" name="modifica" action="" method="POST" >
<?= $oHash->getCamposHtml(); ?>
<input type='hidden' id='mod' name='mod' value=''>
<input type='Hidden' id='que' name='que' value=''>
<table>
<?php
$titulo=strtoupper(_("datos actividad"));
echo "<tr><th colspan='5' class=titulo_inv>$titulo</th>";
if(core\ConfigGlobal::is_app_installed('procesos') && $oPermActiv->only_perm('ver')) echo "<tr><td colspan=5 class='alerta'>"._('no tiene permisos para modificar los datos')."</td></tr>";

//if (($_SESSION['oPerm']->have_perm("des"))) {
if(core\ConfigGlobal::is_app_installed('procesos') && $oPermActiv->only_perm('borrar')) { ?>
<td><input type="Button" value="<?= ucfirst(_("cambiar el tipo")); ?>" onclick="fnjs_tipo_actividad()"></td>
<?php   } ?>
</tr>
<tr>
	<td class=etiqueta><?php echo ucfirst(_("estado")); ?>:</td>
	<?php if (!core\ConfigGlobal::is_app_installed('procesos')) { ?>
		<td><input type="Radio" id="status_1" name="status" value="1" <?php if ($status==1) { echo "checked";} ?>><?php echo _("proyecto"); ?></td>
		<td><input type="Radio" id="status_2" name="status" value="2" <?php if ($status==2) { echo "checked";} ?>><?php echo _("actual"); ?></td>
		<td><input type="radio" id="status_3" name="status" value="3" <?php if ($status==3) { echo "checked";} ?>><?php echo _("terminada"); ?></td>
		<td><input type="radio" id="status_4" name="status" value="4" <?php if ($status==4) { echo "checked";} ?>><?php echo _("borrable"); ?></td>
	<?php } else { ?>
		<!-- Ara faig que no es pugui canviar. S'ha d'anar per les fases.  -->
		<td><?= $a_status[$status] ?></td>
		<input type='hidden' id='status' name='status' value='<?= $status ?>'>
	<?php } ?>
</tr>
<?php 
if (isset($accion) && $accion=="cambiar_tipo") {
	$pagina="actividad_nueva.php"; //esta variable es para la página de abajo: actividad_tipo_que
	include_once ("actividad_tipo_que.php");
} else { ?>
<tr><td class=etiqueta>
	<?php echo ucfirst(_("asistentes")); ?>: </td><td class=contenido><?php echo "$sasistentes<input type='Hidden' id='sasistentes' name='sasistentes' value='$sasistentes'>"?>
</td><td class=etiqueta>
	<?php echo ucfirst(_("actividad")); ?>: </td><td class=contenido><?php echo "$sactividad<input type='Hidden' id='sactividad' name='sactividad' value='$sactividad'>"?>
</td>
</tr><tr>
<td class=etiqueta>
	<?php echo ucfirst(_("tipo actividad")); ?>: </td><td class=contenido>
	<?php 
	//para no hacerse un lio con el generar actividad válido para otros formularios, 
	echo "$snom_tipo<input type='Hidden' id='snom_tipo' name='snom_tipo' value='$snom_tipo'>"?>
</td>
<?php if (($_SESSION['oPerm']->have_perm("vcsd")) or ($_SESSION['oPerm']->have_perm("des"))) { ?>
<td><?php echo _("sf/sv"); ?>: </td><td class=contenido><?php echo "$ssfsv"?></td></tr>
<?php }
} //fin del if cambiar_tipo
$txt_gen=ucfirst(_("generar"));
?>
<tr>
	  <td class=etiqueta><?php echo ucfirst(_("nombre actividad")); ?>:
	  </td><td colspan=8><input class=contenido size='60' id='nom_activ' name='nom_activ' value="<?= htmlspecialchars($nom_activ) ?>">
	  <span class=link onclick="fnjs_generarNomActiv('#modifica');" ><?= $txt_gen ?></span></td>
</td></tr>
<tr>
	  <td class=etiqueta><?php echo ucfirst(_("fecha inicio")); ?>: </td><td><input class=fecha size="11" id="f_ini" name="f_ini" value="<?php echo $f_ini ?>">
	  <td class=etiqueta><?php echo ucfirst(_("hora inicio")); ?>: </td><td><input class=contenido size="6" id="h_ini" name="h_ini" value="<?php echo $h_ini ?>" title="<?= _('formato [hh:mm]') ?>">
</td></tr>
<tr>
	  <td class=etiqueta><?php echo ucfirst(_("fecha fin")); ?>: </td><td><input class=fecha size="11" id="f_fin" name="f_fin" value="<?php echo $f_fin ?>">
	  <td class=etiqueta><?php echo ucfirst(_("hora fin")); ?>: </td><td><input class=contenido size="6" id="h_fin" name="h_fin" value="<?php echo $h_fin ?>" title="<?= _('formato [hh:mm]') ?>">
</td></tr>
<?php
?>
<tr><td class=etiqueta><?= _("dl/r que organiza") ?>:</td>
<td colspan=3>
	<?php echo $oDesplDelegacionesOrg->desplegable(); ?>
	</td>
	</tr>
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
<tr><td class=etiqueta>
<?= ucfirst(_("observaciones")); ?>:</td><td colspan=2><input class=contenido size="30" id="observ" name="observ" value="<?= htmlspecialchars($observ) ?>">
</td></tr>
<tr>
<td class=etiqueta><?php echo ucfirst(_(" repetición anual por")); ?>:</td>
<td colspan=2>
<?php echo $oDesplRepeticion->desplegable(); ?>
</td>
<td class=etiqueta><?php echo ucfirst(_(" nivel de stgr")); ?>:</td>
<td colspan=2>
<?php echo $oDesplNivelStgr->desplegable(); ?>
</td>
</tr><tr>
<td class=etiqueta><?= _("publicado") ?>:</td>
<td><input type="Radio" id="pub_1" name="publicado" value="t" <?php if ($publicado == true) { echo "checked";} ?>><?php echo _("si"); ?>
<input type="Radio" id="pub_2" name="publicado" value="f" <?php if ($publicado == false) { echo "checked";} ?>><?php echo _("no"); ?></td>
</tr>
</table>
<br>
<?php
if ($oPermActiv->have_perm('modificar')) {
	echo "<input TYPE='button' VALUE='".ucfirst(_("guardar cambios"))."'  onclick='fnjs_guardar()'>";
}
?>
</form>
