<?php

use core\ConfigGlobal;
use dbextern\model\GestorIdMatchPersona;
use dbextern\model\PersonaListas;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$dl = (string)  filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');
$ids_traslados_A = (string)  filter_input(INPUT_POST, 'ids_traslados_A');

$a_ids_traslados_A = json_decode(urldecode($ids_traslados_A));

$a_persona_listas = array();
$i = 0;
foreach ($a_ids_traslados_A as $id_nom_listas) {
	$i++;
	$oGesMatch = new GestorIdMatchPersona();
	$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas'=>$id_nom_listas));
	$id_nom_orbix = $cIdMatch[0]->getId_orbix();
	
	//Buscar en otras dl (formato: H-dlpv)
	$oPersonaListas = new PersonaListas($id_nom_listas);
	
	$a_persona_listas[$i]['id_nom_orbix'] = $id_nom_orbix;
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
	$a_persona_listas[$i]['dl'] = "dl". $oPersonaListas->getDl();

}


$url_sincro_ajax = ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
$oHash = new Hash();
$oHash->setUrl($url_sincro_ajax);
//$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('que!dl!id_nom_orbix!tipo_persona'); 
$h = $oHash->linkSinVal();

// ------------------ html ----------------------------------
?>
<script>
fnjs_trasladar=function(id_orbix,dl,fila){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=trasladarA&dl='+dl+'&id_nom_orbix='+id_orbix+'&tipo_persona=<?= $tipo_persona ?><?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
			 
	$.ajax({
		url: url,
		type: 'post',
		data: parametros,
		success: function (rta) {
			if (rta != true) { 
				alert ('respuesta: '+rta);
			} else {
				//tachar la fila
				$("#fila"+fila).addClass('tachado');
			}
		}
	});
}

</script>

<h3><?= _("Personas en otras dl en Listas") ?></h3>
<table>
	<tr><th><?= _("nombre") ?></th><th><?= _("dl actual") ?></th><th></th></tr>
<?php
	$i = 0;
	foreach($a_persona_listas as $persona_listas) {
		$i++;
		$id_orbix = $persona_listas['id_nom_orbix'];
		$dl_actual = $persona_listas['dl'];
		echo "<tr id=fila$i>";
		echo "<td class='titulo'>".$persona_listas['ape_nom'].'</td>';
		echo "<td>".$dl_actual.'</td>';
		echo "<td><span class=link onClick='fnjs_trasladar($id_orbix,\"$dl_actual\",$i)'>" . _("trasladar") . '</span><td>';
		echo '</tr>';
	}
?>
</table>