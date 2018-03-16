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
$ids_desaparecidos_de_orbix = (string)  filter_input(INPUT_POST, 'ids_desaparecidos_de_orbix');

$a_ids_desaparecidos_de_orbix = json_decode(urldecode($ids_desaparecidos_de_orbix));


$a_persona_listas = array();
$i = 0;
foreach ($a_ids_desaparecidos_de_orbix as $id_nom_listas) {
	$i++;
	
	$oPersonaListas = new dbextern\model\PersonaListas($id_nom_listas);

	$a_persona_listas[$i]['id_nom_listas'] = $id_nom_listas;
	$a_persona_listas[$i]['ape_nom'] = $oPersonaListas->getApeNom();
//	$a_persona_orbix[$i]['nombre'] = $oPersonaOrbix->getNom();
//	$a_persona_orbix[$i]['apellido1'] = $oPersonaOrbix->getApellido1();
//	$a_persona_orbix[$i]['nx1'] = $oPersonaOrbix->getNx1();
//	$a_persona_orbix[$i]['apellido2'] = $oPersonaOrbix->getApellido2();
//	$a_persona_orbix[$i]['nx2'] = $oPersonaOrbix->getNx2();
//	$a_persona_orbix[$i]['f_nacimiento'] = $oPersonaOrbix->getF_nacimiento();
//	$a_persona_orbix[$i]['situacion'] = $oPersonaOrbix->getSituacion();
//	$a_persona_orbix[$i]['f_situacion'] = $oPersonaOrbix->getF_situacion();
	$a_persona_listas[$i]['dl'] = $oPersonaListas->getDl();

}


$url_sincro_ajax = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
$oHash = new web\Hash();
$oHash->setUrl($url_sincro_ajax);
//$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('que!id_nom_orbix!tipo_persona'); 
$h = $oHash->linkSinVal();

// ------------------ html ----------------------------------
?>
<script>
fnjs_desunir=function(id_listas){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=desunir&id_nom_listas='+id_listas+'&tipo_persona=<?= $tipo_persona ?><?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
			 
	$.ajax({
		url: url,
		type: 'post',
		data: parametros,
		success: function (rta) {
			if (rta != true) { 
				alert ('respuesta: '+rta);
			}
			//rta_txt=rta.responseText;
			//alert ('respuesta: '+rta_txt);
			//fnjs_submit('#movimiento','-');
		}
	});
}

</script>

<h3><?= _("Personas de listas que habían estado en orbix y no se encuentran") ?></h3>
<table>
	<tr><th><?= _("nombre") ?></th><th><?= _("dl actual") ?></th><th></th></tr>
<?php
	foreach($a_persona_listas as $persona_listas) {
		$id_listas = $persona_listas['id_nom_listas'];
		$dl = $persona_listas['dl'];
		echo "<tr>";
		echo "<td class='titulo'>".$persona_listas['ape_nom'].'</td>';
		echo "<td>".$dl.'</td>';
		echo "<td><span class=link onClick='fnjs_desunir($id_listas)'>" . _("desunir") . '</span><td>';
		echo '</tr>';
	}
?>
</table>