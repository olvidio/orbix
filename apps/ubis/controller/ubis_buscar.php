<?php
use ubis\model as ubis;
/**
* Es un formulario para introducir las condiciones de búsqueda de los ubis.
*
*
*@package	delegacion
*@subpackage	ubis
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

//regiones posibles
$GesRegion = new ubis\GestorRegion();
$oDesplRegion = $GesRegion->getListaRegiones();
$oDesplRegion->setNombre('region');


// tipo ctr
$oDesplTipoCentro = new web\Desplegable();
$GesTipoCentro = new ubis\GestorTipoCentro();
$a_tipos_centro = $GesTipoCentro->getListaTiposCentro() ;
$oDesplTipoCentro->setNombre('tipo_ctr');
$oDesplTipoCentro->setBlanco(1);
$oDesplTipoCentro->setOpciones($a_tipos_centro);

// tipo casa
$oDesplTipoCasa = new web\Desplegable();
$GesTipoCasa = new ubis\GestorTipoCasa();
$a_tipos_casa = $GesTipoCasa->getListaTiposCasa() ;
$oDesplTipoCasa->setNombre('tipo_casa');
$oDesplTipoCasa->setBlanco(1);
$oDesplTipoCasa->setOpciones($a_tipos_casa);

//paises posibles
$GesPais = new ubis\GestorDireccionCtr();
$oDesplPais = $GesPais->getListaPaises();
$oDesplPais->setNombre('pais');

$simple = empty($_POST['simple'])? 1 : $_POST['simple'];
$tipo = empty($_POST['tipo'])? "tot" : $_POST['tipo'];
$loc = empty($_POST['loc'])? "tot" : $_POST['loc'];

switch ($tipo) {
	case "ctrdl" :
		$titulo=strtoupper_dlb(_("centros de la delegación"));
		$tituloGros=strtoupper_dlb(_("¿qué centro te interesa?"));
		$nomUbi=ucfirst(_("nombre del centro"));
		break;
	case "vu_ex" :
		$titulo=strtoupper(_("centros o casas de otras dl/r"));
		$tituloGros=strtoupper_dlb(_("¿qué centro o casa te interesa?"));
		$nomUbi=ucfirst(_("nombre del centro o casa"));
		break;
	case "ctrex" :
		$titulo=strtoupper(_("centros de otras dl/r"));
		$tituloGros=strtoupper_dlb(_("¿qué centro te interesa?"));
		$nomUbi=ucfirst(_("nombre del centro"));
		break;
	case "cdcdl" :
		$titulo=strtoupper_dlb(_("casas de la delegación"));
		$tituloGros=strtoupper_dlb(_("¿qué casa te interesa?"));
		$nomUbi=ucfirst(_("nombre de la casa"));
		break;
	case "cdcex" :
		$titulo=strtoupper(_("casas de otras dl/r"));
		$tituloGros=strtoupper_dlb(_("¿qué casa te interesa?"));
		$nomUbi=ucfirst(_("nombre de la casa"));
		break;
	case "mail" :
		$titulo=ucfirst(_("buscar e-mails de los centros de la dl"));
		$tituloGros=ucfirst(_("escoge un grupo de centros"));
		$nomUbi=ucfirst(_("nombre del centro"));
		break;
	case "ctrsf" :
		$titulo=strtoupper(_("centros de la sf"));
		$tituloGros=strtoupper_dlb(_("¿qué centro te interesa?"));
		$nomUbi=ucfirst(_("nombre del centro"));
		break;
}
switch ($tipo) {
	case "ctr" :
		$nomUbi=ucfirst(_("nombre del centro"));
		break;
	case "cdc" :
		$nomUbi=ucfirst(_("nombre de la casa"));
		break;
	case "tot" :
		$nomUbi=ucfirst(_("nombre de la casa o centro"));
		break;
}

$oHash = new web\Hash();

$s_camposForm = 'simple!nombre_ubi!opcion!ciudad';
$oHash->setcamposNo('simple!tipo_ctr!tipo_casa');

if ($_POST['simple']==1) {
	$s_camposForm .= '!region!pais';
}
if ($_POST['simple']==2) {
	$s_camposForm .= '!tipo!loc';
	if ($loc=="ex") {
		$s_camposForm .= '!dl!region!pais';
	}
}
$oHash->setcamposForm($s_camposForm);


if ($simple==1) {
	$pagina=web\Hash::link('apps/ubis/controller/ubis_buscar.php?'.http_build_query(array('simple'=>'2'))); 
} else {
	$pagina=web\Hash::link('apps/ubis/controller/ubis_buscar.php?'.http_build_query(array('simple'=>'1'))); 
}

?>
<script>
fnjs_buscar=function(formulario){
	var form_name=$(formulario).attr('name');
	var opcion=form_name.substr(-1);
	fnjs_ver_solo(formulario);
	// borro los posibles resultados anteriores
	$('#resultados').html("");
	$(formulario+" opcion").val(opcion);
	<?php if ($tipo=="mail") { ?>
		$(formulario).attr('action','scdl/tabla_mails.php');
	<?php } else { ?>
		$(formulario).attr('action','apps/ubis/controller/ubis_tabla.php');
	<?php } ?>
	fnjs_enviar_formulario(formulario,'#resultados');
}
fnjs_ver_solo=function(formulario){
	// colección de todos los formularios
	$('#condiciones form').each(function(i,f){
		$(this).hide();
	});
	$(formulario).show();
}

fnjs_actualizar=function(formulario){
	$(formulario).attr('action','apps/ubis/controller/ubis_buscar.php');
	fnjs_enviar_formulario(formulario,'#condiciones');
}
<?php 
if ($simple==1) {
	?>
	fnjs_ver_solo('#frm_buscar_1');
	<?php
}
if ($simple==2) {
	?>
	fnjs_ver_solo('#frm_buscar_2');
	<?php
}
?>
</script>
<div id="condiciones">
<form id="frm_buscar_1" name="frm_buscar_1" action="" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" name="opcion" value="">
<input type="hidden" name="simple" value="1">
<!-- Búsqueda simple --------------------------------------------- -->
<table border=1>
<thead><th class=titulo_inv colspan=4><?php echo ucfirst(_("buscar centro o casa")); ?></th>
</thead>
<tfoot>
<tr>
<td class=etiqueta align="RIGHT"><input type="checkbox" name="cmb"><?= _("buscar ubis fuera de uso"); ?></td>
<td colspan=5 style="text-align:right;">
<input id="ok" name="ok" TYPE="button" VALUE="<?php echo _("buscar"); ?>" onclick="fnjs_buscar('#frm_buscar_1')"  class="btn_ok" ></td></tr>
</tfoot>
<tbody>
<tr>
	<td class=etiqueta><?php echo $nomUbi ?></td>
	<td colspan="4"><input class=contenido id=nombre_ubi name=nombre_ubi size="60"></td>
</tr>
<tr>
	<td class=etiqueta><?php echo ucfirst(_("población")); ?></td>
	<td colspan="4"><input class=contenido id=ciudad name=ciudad size="60"></td>	
</tr>
<tr>
	<td class=etiqueta><?php echo ucfirst(_("región")); ?></td>
	<td><?php echo $oDesplRegion->desplegable(); ?></td>
</tr>
<tr>
	<td class=etiqueta><?php echo ucfirst(_("país")); ?></td>
	<td><?= $oDesplPais->desplegable(); ?></td>
</tr>

<?php
if ($tipo=="mail") { ?>
<table align="justify">
<tr></tr>
<tr>
<td class=subtitulo><?php echo _("tipo de centro"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="a"><?php echo _("agd"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="aj"><?php echo _("agd jóvenes"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="am"><?php echo _("agd mayores"); ?></td>
<td colspan="3" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="n"><?php echo _("numerarios"); ?>
<input type="Checkbox" id="select[]" name="select[]" value="nj"><?php echo _("n jóvenes"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="nm"><?php echo _("n mayores"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="rs"><?php echo _("residencia"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="sg"><?php echo _("san Gabriel"); ?></td>
<td colspan="2" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="ce"><?php echo _("centro de estudios"); ?></td>
</tr>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="oc"><?php echo _("obra corporativa"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="igl"><?php echo _("iglesia"); ?></td>
<td colspan="1" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="cefi"><?php echo _("ce de formación intensa"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="ss"><?php echo _("sss+"); ?></td>
</tr>
<tr>
<td colspan="1" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="cipna"><?php echo _("centro internacional"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="lp"><?php echo _("labor personal"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="cr"><?php echo _("comisiones"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="dl"><?php echo _("delegaciones"); ?></td>
</tr>
<tr>
<td class=subtitulo><?php echo _("tipo labor"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="512"><?php echo _("sr"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="256"><?php echo _("n"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="128"><?php echo _("agd"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="64"><?php echo _("sg"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="32"><?php echo _("sss+"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="16"><?php echo _("club"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="8"><?php echo _("bachilleres"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="4"><?php echo _("universitarios"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="2"><?php echo _("jóvenes"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="1"><?php echo _("mayores"); ?></td>
</tr>
</table>
<?php } ?>
</tbody></table>
</form>

<!-- más opciones --------------------------------------------- -->
<form id="frm_buscar_2" name="frm_buscar_2" action="" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>
<input type="hidden" name="opcion" value="">
<input type="hidden" name="simple" value="2">
<table border=1>
<thead><th class=titulo_inv colspan=4><?php echo ucfirst(_("buscar centro o casa")); ?></th></thead>
<tfoot>
<tr><td colspan=5 style="text-align:right;"><input id="b_buscar_2" name="b_buscar" TYPE="button" VALUE="<?php echo _("buscar"); ?>" onclick="fnjs_buscar('#frm_buscar_2')" class="btn_ok"></td></tr>
</tfoot>
<tbody>
<tr><td class=etiqueta>
<?php echo ucfirst(_("tipo")); ?>
</td><td>
<select id="tipo" name="tipo" onchange="fnjs_actualizar('#frm_buscar_2')" class=contenido>
<?php 
	if ($tipo=="ctr") { $ctr_selected="selected"; } else { $ctr_selected=""; }
	if ($tipo=="cdc") { $cdc_selected="selected"; } else { $cdc_selected=""; }
	if ($tipo=="tot") { $tot_selected="selected"; } else { $tot_selected=""; }
	echo "<option value='ctr' $ctr_selected>".ucfirst(_("centro"));
	echo "<option value='cdc' $cdc_selected>".ucfirst(_("casa")); 
	echo "<option value='tot' $tot_selected>".ucfirst(_("todos")); ?>
	</select>
</td><td>
<?php echo ucfirst(_("localización")); ?>
</td><td>
<select id="loc" name="loc" onchange="fnjs_actualizar('#frm_buscar_2')" class=contenido>
<?php
	if ($loc=="dl") { $dl_selected="selected"; } else { $dl_selected=""; }
	if ($loc=="ex") { $ex_selected="selected"; } else { $ex_selected=""; }
	if ($loc=="tot") { $to_selected="selected"; } else { $to_selected=""; }
	echo "<option value='dl' $dl_selected>"._("de dl");
	echo "<option value='ex' $ex_selected>"._("de otra dl/cr");
	echo "<option value='tot' $to_selected>"._("todos"); 
	/* de momento lo anulo. ahy que ver si funcions desde sf
	if (($_SESSION['oPerm']->have_perm("des")) || ($_SESSION['oPerm']->have_perm("vcsd"))) {
		if ($_POST['loc']=="sf") { $sf_selected="selected"; } else { $sf_selected=""; }
		echo "<option value='sf' $sf_selected>"._("de la sf");
	}
	*/
	?>
</select>
</td></tr>
<tr>
	<td class=etiqueta><?php echo $nomUbi ?></td>
	<td colspan="4"><input class=contenido id=nombre_ubi name=nombre_ubi size="60"></td>
</tr>
<tr>
	<td class=etiqueta><?php echo ucfirst(_("población")); ?></td>
	<td colspan="4"><input class=contenido id=ciudad name=ciudad size="60"></td>	
</tr>

<?php if ($loc=="ex") { ?>
	<tr>
		<td class=etiqueta><?php echo _("dl"); ?></td>
		<td><input class=contenido id=dl name=dl size=1  style="HEIGHT: 22px; WIDTH: 62px"></td> 
		<td class=etiqueta><?php echo ucfirst(_("región")); ?></td>
		<td><input class=contenido id=region name=region size=1 style="HEIGHT: 22px; WIDTH: 62px"></td> 
	</tr>
	<tr>
		<td class=etiqueta><?php echo ucfirst(_("país")); ?></td>
		<td colspan="4"><input class=contenido id=pais name=pais size=1 style="HEIGHT: 22px; WIDTH: 250px"></td>
	</tr>
	<?php

	if ($tipo=="ctr" ) {
	?>
		<tr><td class=etiqueta><?php echo _("tipo de centro"); ?></td>
		<td><?= $oDesplTipoCentro->desplegable(); ?>
		</td></tr>
	<?php
	}
	if ($tipo=="cdc" ) {
	?>
		<tr><td class=etiqueta><?php echo _("tipo de casa"); ?></td>
		<td><?= $oDesplTipoCasa->desplegable(); ?>
		</td></tr>
	<?php	
	}
}

if ($tipo=="mail") { ?>
<table align="justify">
<tr></tr>
<tr></tr>
<tr>
<td class=subtitulo><?php echo _("tipo de centro"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="a"><?php echo _("agd"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="aj"><?php echo _("agd jóvenes"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="am"><?php echo _("agd mayores"); ?></td>
<td colspan="3" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="n"><?php echo _("numerarios"); ?>
<input type="Checkbox" id="select[]" name="select[]" value="nj"><?php echo _("n jóvenes"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="nm"><?php echo _("n mayores"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="rs"><?php echo _("residencia"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="sg"><?php echo _("san Gabriel"); ?></td>
<td colspan="2" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="ce"><?php echo _("centro de estudios"); ?></td>
</tr>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="oc"><?php echo _("obra corporativa"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="igl"><?php echo _("iglesia"); ?></td>
<td colspan="1" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="cefi"><?php echo _("ce de formación intensa"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="ss"><?php echo _("sss+"); ?></td>
</tr>
<tr>
<td colspan="1" class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="cipna"><?php echo _("centro internacional"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="lp"><?php echo _("labor personal"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="cr"><?php echo _("comisiones"); ?></td>
<td class=etiqueta><input type="Checkbox" id="select[]" name="select[]" value="dl"><?php echo _("delegaciones"); ?></td>
</tr>
<tr>
<td class=subtitulo><?php echo _("tipo labor"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="512"><?php echo _("sr"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="256"><?php echo _("n"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="128"><?php echo _("agd"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="64"><?php echo _("sg"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="32"><?php echo _("sss+"); ?></td>
</tr>
<tr>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="16"><?php echo _("club"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="8"><?php echo _("bachilleres"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="4"><?php echo _("universitarios"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="2"><?php echo _("jóvenes"); ?></td>
<td class=etiqueta><input type="Checkbox" id="labor[]" name="labor[]" value="1"><?php echo _("mayores"); ?></td>
</tr>
</table>
<?php
}
?>
</tbody></table>
</form>
<td><input id="b_mas" name="b_mas" TYPE="button" VALUE="<?php echo _("ver otras opciones"); ?>" onclick="fnjs_update_div('#main','<?= $pagina ?>')" ></td>
</table>
</div>
<div id="resultados">
</div>
