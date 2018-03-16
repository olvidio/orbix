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
$ids_traslados = (string)  filter_input(INPUT_POST, 'ids_traslados');

$a_ids_traslados = json_decode(urldecode($ids_traslados));

$id_tipo = 0;
switch ($tipo_persona) {
	case 'n':
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$id_tipo = 1;
//			$obj_pau = 'GestorPersonaN';
			$obj_pau = 'PersonaN';
		}
		break;
	case 'a':
		if ($_SESSION['oPerm']->have_perm("agd")) {
			$id_tipo = 2;
//			$obj_pau = 'GestorPersonaAgd';
			$obj_pau = 'PersonaAgd';
		}
		break;
	case 's':
		if ($_SESSION['oPerm']->have_perm("sg")) {
			$id_tipo = 3;
//			$obj_pau = 'GestorPersonaS';
			$obj_pau = 'PersonaS';
		}
		break;
}

$obj = 'personas\\model\\'.$obj_pau;

$oSincroDB = new SincroDB();
$oSincroDB->setTipo_persona($tipo_persona);

$i = 0;
foreach ($a_ids_traslados as $id_nom_orbix) {
	$i++;
	//Buscar en otras dl (formato: H-dlpv)
	$dl_orbix = $oSincroDB->buscarEnOrbix($id_nom_orbix);
	$a_reg_dl = explode('-',$dl_orbix);
	$dl_actual = substr($a_reg_dl[1],0,-1); // quito la v o la f.
	$a_persona_orbix[$i]['dl_actual'] = $dl_actual;
	
	$oPersonaOrbix = new $obj(array('id_nom'=>$id_nom_orbix));
	// buscar la persona en otradl
	$oDB = $oSincroDB->conexion($dl_orbix);
	$oPersonaOrbix->setoDbl($oDB);

	$a_persona_orbix[$i]['id_nom_orbix'] = $id_nom_orbix;
	$a_persona_orbix[$i]['ape_nom'] = $oPersonaOrbix->getApellidosNombre();
	$a_persona_orbix[$i]['dl'] = $oPersonaOrbix->getDl();

	$oSincroDB->restaurarConexion($oDB);
}


$url_sincro_ajax = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
$oHash = new web\Hash();
$oHash->setUrl($url_sincro_ajax);
//$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('que!dl!id_nom_orbix!tipo_persona'); 
$h = $oHash->linkSinVal();

$txt_alert =_("Se va a poner la fecha de hoy como fecha de traslado. Para cambiarlo ir al a ficha de la persona y al dossier de traslados")
// ------------------ html ----------------------------------
?>
<script>
fnjs_trasladar=function(id_orbix,dl,fila){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=trasladar&dl='+dl+'&id_nom_orbix='+id_orbix+'&tipo_persona=<?= $tipo_persona ?><?= $h ?>&PHPSESSID=<?php echo session_id(); ?>';
	
	alert ("<?= $txt_alert ?>");		 
	
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

<h3><?= _("Personas en la dl que deben ser trasladadas desde otras dl") ?></h3>
<table>
	<tr><th><?= _("nombre") ?></th><th><?= _("dl orbix") ?></th><th></th></tr>
<?php
	$i = 0;
	foreach($a_persona_orbix as $persona_orbix) {
		$i++;
		$id_orbix = $persona_orbix['id_nom_orbix'];
		$dl = $persona_orbix['dl'];
		echo "<tr id=fila$i>";
		echo "<td class='titulo'>".$persona_orbix['ape_nom'].'</td>';
		echo "<td>".$persona_orbix['dl_actual'].'</td>';
		echo "<td><span class=link onClick='fnjs_trasladar($id_orbix,\"$dl\",$i)'>" . _("trasladar") . '</span><td>';
		echo '</tr>';
	}
?>
</table>