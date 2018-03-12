<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mov = '';

$dl = (string)  filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');
$ids_orbix_text = (string)  filter_input(INPUT_POST, 'ids_orbix_text');

$a_ids_orbix = json_decode(urldecode($ids_orbix_text));

$id = (string)  filter_input(INPUT_POST, 'id');
$mov = (string)  filter_input(INPUT_POST, 'mov');
//$tipo_persona = 'a';

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

function otro($id,$mov,$max) {
	switch($mov) {
		case '-':
			$id--;
			if ($id < 1) {
				return 1;
			}
			break;
		case '+':
			$id++;
			if ($id > $max) {
				return $max;
			}
			break;
		default:
			$id = 1;
	}
	if (isset($_SESSION['DBOrbix'][$id])) {
		return $id;
	} else {
		return otro($id,$mov,$max);
	}
}



if (empty($id)) {
	$id=1;
	$obj = 'personas\\model\\'.$obj_pau;
	$i = 0;
	foreach ($a_ids_orbix as $id_nom_orbix) {
		$oPersonaOrbix = new $obj(array('id_nom'=>$id_nom_orbix));

		$a_persona_orbix['id_nom_orbix'] = $id_nom_orbix;
		$a_persona_orbix['ape_nom'] = $oPersonaOrbix->getApellidosNombre();
		$a_persona_orbix['nombre'] = $oPersonaOrbix->getNom();
		$a_persona_orbix['apellido1'] = $oPersonaOrbix->getApellido1();
		$a_persona_orbix['nx1'] = $oPersonaOrbix->getNx1();
		$a_persona_orbix['apellido2'] = $oPersonaOrbix->getApellido2();
		$a_persona_orbix['nx2'] = $oPersonaOrbix->getNx2();
		$a_persona_orbix['f_nacimiento'] = $oPersonaOrbix->getF_nacimiento();
		$a_persona_orbix['situacion'] = $oPersonaOrbix->getSituacion();
		$a_persona_orbix['f_situacion'] = $oPersonaOrbix->getF_situacion();

		// incremento antes para empezar en 1 y no en 0.
		$i++;
		$a_lista[$i] = $a_persona_orbix;
	}
	$_SESSION['DBOrbix'] = $a_lista;
}


$max = count($_SESSION['DBOrbix']);

$new_id = otro($id,$mov,$max);
$persona_orbix = $_SESSION['DBOrbix'][$new_id];

$url_sincro_situacion = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ver_situacion.php';
$oHash = new web\Hash();
$oHash->setUrl($url_sincro_situacion);
$oHash->setcamposNo('mov');
$a_camposHidden = array(
		'dl' => $dl,
		'tipo_persona' => $tipo_persona,
		'id' => $new_id,
		);
$oHash->setArraycamposHidden($a_camposHidden);

//$url_sincro_ajax = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
//$oHash1 = new web\Hash();
//$oHash1->setUrl($url_sincro_ajax);
//$oHash1->setCamposForm('que!id_nom_listas!id!id_orbix'); 
//$h1 = $oHash1->linkSinVal();

$url_sincro_ajax = web\Hash::link('apps/dbextern/controller/sincro_ajax.php?'.http_build_query(array('dl'=>$dl,'tipo_persona'=>$tipo_persona,'id_nom_orbix' => $id_nom_orbix)));

$html_reg = sprintf(_("registro %s de %s"),$new_id,$max);
// ------------------ html ----------------------------------
?>
<script>

fnjs_submit=function(formulario,mov){

	$('#mov').val(mov);
	
	$(formulario).attr('action',"<?= $url_sincro_situacion ?>");
  	fnjs_enviar_formulario(formulario);
}
</script>

<h3><?= _("Personas en Orbix con situacion distinta a Listas") ?></h3>

<form id="movimiento" name="movimiento" action="">
	<?= $oHash->getCamposHtml(); ?>
	<input type="hidden" id="mov" name="mov" value="">
	<input type="button" value="< <?= _("anterior") ?>" onclick="fnjs_submit(this.form,'-')" />
	<?= $html_reg ?>
	<input type="button" value="<?= _("siguiente") ?> >" onclick="fnjs_submit(this.form,'+')" />
	<br>
	<br>
	
<table>
<?php
	echo "<tr>";
	echo "<td>".$persona_orbix['id_nom_orbix'].'<td>';
	echo "<td class='titulo'>".$persona_orbix['ape_nom'].'<td>';
	echo "<td>".$persona_orbix['nombre'].'<td>';
	echo "<td>".$persona_orbix['apellido1'].'<td>';
	echo "<td>".$persona_orbix['apellido2'].'<td>';
	echo "<td>".$persona_orbix['situacion'].'<td>';
	echo "<td>".$persona_orbix['f_situacion'].'<td>';
	echo "<td class='titulo'>".$persona_orbix['f_nacimiento'].'<td>';
	echo '</tr>';
?>
</table>
<br>
Por el momento estos botones no hacen nada.
<input type="button" value="<?= _("trasladar") ?>" onclick="fnjs_update_div('#main','<?= $url_sincro_ajax ?>')">
</form>