<?php
use dbextern\model\SincroDB;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$dl = (string)  filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');
$ids_desaparecidos_de_listas = (string)  filter_input(INPUT_POST, 'ids_desaparecidos_de_listas');

$a_ids_desaparecidos_de_listas = json_decode(urldecode($ids_desaparecidos_de_listas));


$a_persona_orbix = array();
$i = 0;
foreach ($a_ids_desaparecidos_de_listas as $id_nom_orbix) {
	$i++;
	$oPersonaDl = new personas\model\entity\PersonaDl($id_nom_orbix);

	$a_persona_orbix[$i]['id_nom_orbix'] = $id_nom_orbix;
	$a_persona_orbix[$i]['ape_nom'] = $oPersonaDl->getApellidosNombre();
	$a_persona_orbix[$i]['dl'] = $oPersonaDl->getDl();
}


$url_sincro_ajax = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
$oHash = new web\Hash();
$oHash->setUrl($url_sincro_ajax);
//$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('que!id_nom_orbix!tipo_persona'); 
$h = $oHash->linkSinVal();

$txt_alert =_("se va a poner la fecha de hoy como fecha de baja. Para cambiarlo ir al a ficha de la persona")
// ------------------ html ----------------------------------
?>
<script>
fnjs_baja=function(id_orbix,fila){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=baja&id_nom_orbix='+id_orbix+'&tipo_persona=<?= $tipo_persona ?><?= $h ?>';
			 
	alert ("<?= $txt_alert ?>");

	$.ajax({
		url: url,
		type: 'post',
		data: parametros,
		success: function (rta) {
			rta_txt=rta.responseText;
			if (rta != true) { 
				alert ('<?= _("respuesta") ?>: '+rta_txt);
			} else {
				//tachar la fila
				$("#fila"+fila).addClass('tachado');
			}
		}
	});
}
fnjs_traslado=function($id_orbix){
	//ir la dossier traslado de la persona
	alert ("debiería ir al dossier de traslado de la persona");
}

</script>

<h3><?= _("personas de orbix que habían estado en listas y no se encuentran") ?></h3>
<table>
	<tr><th><?= _("nombre") ?></th><th><?= _("dl") ?></th><th></th></tr>
<?php
	$i = 0;
	foreach($a_persona_orbix as $persona_orbix) {
		$i++;
		$id_orbix = $persona_orbix['id_nom_orbix'];
		$dl = $persona_orbix['dl'];
		echo "<tr id=fila$i>";
		echo "<td class='titulo'>".$persona_orbix['ape_nom'].'</td>';
		echo "<td>".$dl.'</td>';
		echo "<td><span class=link onClick='fnjs_baja($id_orbix,$i)'>" . _("baja") . '</span><td>';
		echo "<td><span class=link onClick='fnjs_traslado($id_orbix,$i)'>" . _("fallecido o traslado a otra r") . '</span><td>';
		echo '</tr>';
	}
?>
</table>
