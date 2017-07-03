<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Debo incluuirlo aqui por que se abre en una página nueva
include_once(core\ConfigGlobal::$dir_estilos.'/todo_en_uno.css.php');

$ssfsv = empty($_REQUEST['ssfsv'])? '' : $_REQUEST['ssfsv'];
switch($ssfsv){
	case "sv":
		$donde_sfsv="AND sv='t'";
		break;
	case "sf":
		$donde_sfsv="AND sf='t'";
		break;
	default:
		$donde_sfsv='';
}

if (!empty($_REQUEST['dl_org'])) {
	$sql_freq="select distinct id_ubi,nombre_ubi from a_actividades_dl join u_cdc_dl using (id_ubi) where dl_org='".$_REQUEST['dl_org']."' $donde_sfsv ORDER by nombre_ubi";
	$oDbl = $GLOBALS['oDBC'];
	$oDBSt_q_freq=$oDbl->query($sql_freq);
	$oDesplFreq = new web\Desplegable();
	$oDesplFreq->setNombre('id_ubi_1');
	$oDesplFreq->setOpciones($oDBSt_q_freq);
}

// desplegable región
$oDbl = $GLOBALS['oDBPC'];
$sql_dl_lugar="SELECT 'dl|'||u.dl,u.nombre_dl FROM xu_dl u WHERE status='t' ";
$sql_r_lugar="SELECT 'r|'||u.region,u.nombre_region FROM xu_region u WHERE status='t' ";
$sql_u_lugar=$sql_dl_lugar." UNION ".$sql_r_lugar." ORDER BY 2";
$oDBSt_dl_r_lugar=$oDbl->query($sql_u_lugar);

$oDesplRegion = new web\Desplegable();
$oDesplRegion->setNombre('filtro_lugar');
$oDesplRegion->setAction('fnjs_lugar()');
$oDesplRegion->setOpciones($oDBSt_dl_r_lugar);
if (!empty($_REQUEST['dl_org'])) {
	$oDesplRegion->setOpcion_sel($_REQUEST['dl_org']);
}

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php');
$oHash->setCamposForm('salida!entrada!isfsv');
$h = $oHash->linkSinVal();

$oHash1 = new web\Hash();
$oHash1->setcamposForm('id_ubi_1');
$oHash2 = new web\Hash();
$oHash2->setcamposForm('filtro_lugar!lst_lugar');
$oHash3 = new web\Hash();
$oHash3->setcamposForm('nombre_ubi');
$a_camposHidden = array(
		'tipo' =>'tot',
		'loc' => 'tot'
		);
$oHash3->setArraycamposHidden($a_camposHidden);

$oHash4 = new web\Hash();
$oHash4->setcamposForm('frm_4_nombre_ubi');
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- jQuery -->
<script type="text/javascript" src='<?php echo core\ConfigGlobal::$web_scripts.'/jquery-ui-latest/js/jquery-1.7.1.min.js'; ?>'></script>
<script type="text/javascript" src='<?php echo core\ConfigGlobal::$web_scripts.'/jquery-ui-latest/js/jquery-ui-1.8.17.custom.min.js'; ?>'></script>
</head>

<h2><?= _("buscar una casa") ?></h2>
<?php  echo ucfirst(_("hay 5 opciones diferentes para buscar una casa")); ?>
<!-- casas usadas anteriormente por esta delegación --------------------------------------------- -->
<form id="frm_buscar_1" name="frm_buscar_1" action="">
<?= $oHash1->getCamposHtml(); ?>
<table border=1 style="width: 95%;">
<tr><th colspan=2 class=titulo_inv><?php  echo ucfirst(_("opción 1: posibles lugares (por historial)")); ?></th></tr>
<tr><td class="etiqueta"><?= _("más frequentes") ?></td>
	<td><?php 
	if (!empty($oDesplFreq) && is_object($oDesplFreq)) {
		echo $oDesplFreq->desplegable();
	} else {
		echo _('falta saber quien organiza');
	}
	?></td>
</tr>
<tr><td colspan=2 style="text-align:right;"><input id="b_buscar_1" name="b_buscar" TYPE="button" VALUE="<?php echo _("seleccionar"); ?>" onclick="fnjs_buscar('#frm_buscar_1')" ></td></tr>
</table>
</form>
<!-- -------- por la región a la que pertenece --------------------------------------------- -->
<form id="frm_buscar_2" name="frm_buscar_2" action="">
<?= $oHash2->getCamposHtml(); ?>
<table border=1 style="width: 95%;">
<tr><th colspan=2 class=titulo_inv><?php  echo ucfirst(_("opción 2: según a la región a la que pertenece")); ?></th></tr>
<td class=etiqueta><?php echo _("según dl o r"); ?>:</td>
	<td colspan=2><?php echo $oDesplRegion->desplegable(); ?></td></tr>
<tr><td class=etiqueta><?php echo _("lugar"); ?></td>
	<td id='lst_lugar'></td>
</tr>
<tr><td colspan=2 style="text-align:right;"><input id="b_buscar_2" name="b_buscar" TYPE="button" VALUE="<?php echo _("seleccionar"); ?>" onclick="fnjs_buscar('#frm_buscar_2')" ></td></tr>
</table>
</form>
<!-- Origen, destino  más periodo --------------------------------------------- -->
<form id="frm_buscar_3" name="frm_buscar_3" action="">
<?= $oHash3->getCamposHtml(); ?>
<table border=1 style="width: 95%;">
<tr><th colspan=2 class=titulo_inv><?php  echo ucfirst(_("opción 3: buscar por el nombre")); ?></th></tr>
<tr>
	<td class=etiqueta><?= _("nombre del lugar") ?></td>
	<td colspan="1"><input class=contenido id=nombre_ubi name=nombre_ubi size="30"></td>
</tr>

<tr><td colspan=2 style="text-align:right;"><input id="b_buscar_2" name="b_buscar" TYPE="button" VALUE="<?php echo _("buscar"); ?>" onclick="fnjs_enviar_form('#frm_buscar_3','#lst_lugares')" ></td></tr>
<tr><td id="lst_lugares" colspan=2></td></tr>

</table>
</form>
<!-- Lugares especiales --------------------------------------------- -->
<form id="frm_buscar_4" name="frm_buscar_4" action="">
<?= $oHash4->getCamposHtml(); ?>
<table border=1 style="width: 95%;">
<tr><th colspan=2 class=titulo_inv><?php  echo ucfirst(_("opción 4: Un lugar especial (sin dirección posible)")); ?></th></tr>
<tr>
	<td class=etiqueta><?= _("nombre del lugar") ?></td>
	<td colspan="1"><input class=contenido id=frm_4_nombre_ubi name=frm_4_nombre_ubi size="30"></td>
</tr>

<tr><td colspan=2 style="text-align:right;"><input id="b_buscar_4" name="b_buscar" TYPE="button" VALUE="<?php echo _("seleccionar"); ?>" onclick="fnjs_buscar('#frm_buscar_4')" ></td></tr>
</table>
</form>
<!-- Lugares especiales --------------------------------------------- -->
<form id="frm_buscar_5" name="frm_buscar_5" action="">
<table border=1 style="width: 95%;">
<tr><th colspan=2 class=titulo_inv><?php  echo ucfirst(_("opción 5: Por determinar")); ?></th></tr>
<tr><td colspan=5 style="text-align:right;"><input id="b_buscar_5" name="b_buscar" TYPE="button" VALUE="<?php echo _("seleccionar"); ?>" onclick="fnjs_buscar('#frm_buscar_5')" ></td></tr>
</table>
</form>
<script>
fnjs_enviar_form=function(id_form,bloque){
	if (!bloque) { bloque='#main'; }
	$(id_form).attr('action','<?= core\ConfigGlobal::getWeb().'/apps/ubis/controller/ubis_lista.php' ?>');
	$(id_form).submit(function() {
		$.ajax({
			data: $(this).serialize(),
			url: $(this).attr('action'),
			type: 'post',
			complete: function (rta) {
				rta_txt=rta.responseText;
				$(bloque).html(rta_txt);
			}
		});
		return false;
	});
	$(id_form).submit();
	$(id_form).off();
}

fnjs_lugar=function(){
	//var sfsv=$('#sfsv_val').val();
	var isfsv=<?php
		switch ($ssfsv) {
			case "sv":
				echo 1;
				break;
			case "sf":
				echo 2;
				break;
			default:
				echo 0;
		}
	?>;
	var filtro_lugar=$('#filtro_lugar').val();
	var url='<?= core\ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php' ?>';
	var parametros='salida=lugar&entrada='+filtro_lugar+'&isfsv='+isfsv+'<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#lst_lugar').html(rta_txt);
		}
	});
}

fnjs_buscar=function(formulario,id){
	var form_name=$(formulario).attr('name');
	var opcion=form_name.substr(-1);
	if (opcion==1) {
		var lista=$('#id_ubi_1').val();
		var txt=$('#id_ubi_1 :selected').text();
		if (!lista) {
			alert ("<?= _("Tiene que seleccionar un casa") ?>");
			return;
		}
	  	window.opener.$('#id_ubi').val(lista);
	  	window.opener.$('#nombre_ubi').val(txt);
		window.opener.$('#span_nom_ubi').html(txt);
	  	window.close();

	}
	if (opcion==2) {
		/* OJO. este id_ubi no puede tener otro nombre, porque viene de una página
		*  "actividad_tipo_get.php" que también da los datos a otros programas.
		*/
		var lista=$('#id_ubi').val();
		var txt=$('#id_ubi :selected').text();
		if (!lista) {
			alert ("<?= _("Tiene que seleccionar un casa") ?>");
			return;
		}
	  	window.opener.$('#id_ubi').val(lista);
	  	window.opener.$('#nombre_ubi').val(txt);
		window.opener.$('#span_nom_ubi').html(txt);
	  	window.close();

	}
	if (opcion==3) {
		var lista=id;
		var txt=$('#'+id).html();;
		if (!lista) {
			alert ("<?= _("Tiene que seleccionar un casa") ?>");
			return;
		}
	  	window.opener.$('#id_ubi').val(lista);
	  	window.opener.$('#nombre_ubi').val(txt);
		window.opener.$('#span_nom_ubi').html(txt);
	  	window.close();
	}
	if (opcion==4) {
		/* OJO. este id_ubi no puede tener otro nombre, porque viene de una página
		*  "actividad_tipo_get.php" que también da los datos a otros programas.
		*/
		var txt=document.frm_buscar_4.frm_4_nombre_ubi.value;
		if (!txt) {
			alert ("<?= _("Tiene que escribir un lugar") ?>");
			return;
		}
	  	window.opener.$('#id_ubi').val(1);
		window.opener.$('#span_nom_ubi').html(txt);
		window.opener.$('#lugar_esp').val(txt);
	  	window.close();
	}
	if (opcion==5) {
		/* OJO. este id_ubi no puede tener otro nombre, porque viene de una página
		*  "actividad_tipo_get.php" que también da los datos a otros programas.
		*/
		var txt="<?= _("sin determinar") ?>";
		if (!txt) {
			alert ("<?= _("Tiene que escribir un lugar") ?>");
			return;
		}
	  	window.opener.$('#id_ubi').val("");
		window.opener.$('#span_nom_ubi').html(txt);
		window.opener.$('#lugar_esp').val("");
	  	window.close();
	}
}

</script>
<script>
fnjs_lugar();
</script>
