<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_tipo_activ = empty($_POST['id_tipo_activ'])? '' : $_POST['id_tipo_activ'];

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