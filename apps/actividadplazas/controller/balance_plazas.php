<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_tipo_activ = (string)  filter_input(INPUT_POST, 'id_tipo_activ');
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
	if (empty($_POST['ssfsv'])) {
		$mi_sfsv = core\ConfigGlobal::mi_sfsv();
		if ($mi_sfsv == 1) $_POST['ssfsv'] = 'sv';
		if ($mi_sfsv == 2) $_POST['ssfsv'] = 'sf';
	}
	$ssfsv = $_POST['ssfsv'];
	$sasistentes = empty($_POST['sasistentes'])? '.' : $_POST['sasistentes'];
	$sactividad = empty($_POST['sactividad'])? '.' : $_POST['sactividad'];
	$snom_tipo = empty($_POST['snom_tipo'])? '...' : $_POST['snom_tipo'];
	$oTipoActiv= new web\TiposActividades();
	$oTipoActiv->setSfsvText($ssfsv);
	$oTipoActiv->setAsistentesText($sasistentes);
	$oTipoActiv->setActividadText($sactividad);
	$Qid_tipo_activ=$oTipoActiv->getId_tipo_activ();
}

$aRegiones = array('H');
$gesDelegacion = new ubis\model\GestorDelegacion();
$desplDelegaciones = $gesDelegacion->getListaDelegaciones($aRegiones);
$desplDelegaciones->setNombre("dl");
$desplDelegaciones->setAction("fnjs_comparativa()");

$mi_dele = core\ConfigGlobal::mi_dele();
$txt = sprintf(_("comparar %s con:"),$mi_dele);

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb().'/apps/actividadplazas/controller/balance_dl.php');
$oHash->setCamposForm('dl!Qid_tipo_activ');
$h = $oHash->linkSinVal();


?>
<script>
fnjs_comparativa=function(){
	var filtro_dl=$('#dl').val();
	var url='<?= core\ConfigGlobal::getWeb().'/apps/actividadplazas/controller/balance_dl.php' ?>';
	var parametros='Qid_tipo_activ=<?= $Qid_tipo_activ ?>&dl='+filtro_dl+'<?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#comparativa').html(rta_txt);
		}
	});
}
</script>
<div id='select_dl'>
<?= $txt ?>
	<?= $desplDelegaciones->desplegable(); ?>
</div>
<br>
<div id='comparativa'></div>