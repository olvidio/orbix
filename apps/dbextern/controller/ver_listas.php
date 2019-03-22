<?php
use dbextern\model;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mov = '';

$region = (string)  filter_input(INPUT_POST, 'region');
$dl = (string)  filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo_persona');

$id = (string)  filter_input(INPUT_POST, 'id');
$mov = (string)  filter_input(INPUT_POST, 'mov');

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
	if (isset($_SESSION['DBListas'][$id])) {
		return $id;
	} else {
		return otro($id,$mov,$max);
	}
}

$oSincroDB = new dbextern\model\sincroDB();
$oSincroDB->setTipo_persona($tipo_persona);
$oSincroDB->setRegion($region);
$oSincroDB->setDl($dl);

$id_nom_listas = '';
if (empty($id)) {
	$id=1;
	// todos los de listas
	$cPersonasListas = $oSincroDB->getPersonasListas();
	
	$i = 0;
	$cont_sync = 0;
	$a_lista = [];
	foreach ($cPersonasListas as $oPersonaListas) {
		$id_nom_listas = $oPersonaListas->getIdentif();

		$oGesMatch = new dbextern\model\entity\GestorIdMatchPersona();
		$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas'=>$id_nom_listas));
		if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
			continue;
		}
		// Sólo la primera vez (mov = ''):
		if (empty($mov) && $oSincroDB->union_automatico($oPersonaListas)) {
			$cont_sync++;
			continue;
		}

		$a_persona_lista['id_nom_listas'] = $id_nom_listas;
		$a_persona_lista['ape_nom'] = $oPersonaListas->getApeNom();
		$a_persona_lista['nombre'] = $oPersonaListas->getNombre();
		$a_persona_lista['apellido1'] = $oPersonaListas->getApellido1();
		$a_persona_lista['apellido1_sinprep'] = $oPersonaListas->getApellido1_sinprep();
		$a_persona_lista['apellido2'] = $oPersonaListas->getApellido2();
		$a_persona_lista['apellido2_sinprep'] = $oPersonaListas->getApellido2_sinprep();
		$a_persona_lista['f_nacimiento'] = $oPersonaListas->getFecha_Naci();
		// incremento antes para empezar en 1 y no en 0.
		$i++;
		$a_lista[$i] = $a_persona_lista;
	}
	$_SESSION['DBListas'] = $a_lista;
}

$max = count($_SESSION['DBListas']);

$a_lista_orbix = array();
$persona_listas = array();
$a_lista_orbix_otradl = array();
$new_id = 0;
if (!empty($max)) { $new_id = otro($id,$mov,$max); }
// Buscar coincidentes en orix
// assegurar que existe (al llegar al final)
if (!empty($new_id) && isset($_SESSION['DBListas'][$new_id])) {
	$persona_listas = $_SESSION['DBListas'][$new_id];
	$id_nom_listas = $persona_listas['id_nom_listas'];
	
	$a_lista_orbix =$oSincroDB->posiblesOrbix($id_nom_listas);
	//si no encuentro, mirar en otras dl
	if (empty($a_lista_orbix)) {
		$a_lista_orbix_otradl =$oSincroDB->posiblesOrbixOtrasDl($id_nom_listas);
	}
}

$url_sincro_ver = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/ver_listas.php';
$oHash = new web\Hash();
$oHash->setUrl($url_sincro_ver);
$oHash->setcamposNo('mov');
$a_camposHidden = array(
		'region' => $region,
		'dl' => $dl,
		'tipo_persona' => $tipo_persona,
		'id' => $new_id,
		);
$oHash->setArraycamposHidden($a_camposHidden);

$url_sincro_ajax = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url_sincro_ajax);
//$oHash1->setArraycamposHidden($a_camposHidden);
$oHash1->setCamposForm('que!id_nom_listas!id_orbix!region!dl!id!tipo_persona'); 
$h1 = $oHash1->linkSinVal();


$html_reg = sprintf(_("registro %s de %s"),$new_id,$max);
// ------------------ html ----------------------------------
?>
<script>
fnjs_crear=function(){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=crear&region=<?= $region ?>&dl=<?= $dl ?>&id=<?= $new_id?>&id_nom_listas=<?= $id_nom_listas ?>&id_orbix=&tipo_persona=<?= $tipo_persona ?><?= $h1 ?>';
			 
	$.ajax({
		url: url,
		type: 'post',
		data: parametros
	})
	.done(function (rta_txt) {
		fnjs_submit('#movimiento','-');
	});
}

fnjs_unir=function(id_orbix){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=unir&region=<?= $region ?>&dl=<?= $dl ?>&id_orbix='+id_orbix+'&id=<?= $new_id?>&id_nom_listas=<?= $id_nom_listas ?>&tipo_persona=<?= $tipo_persona ?><?= $h1 ?>';
			 
	$.ajax({
		url: url,
		type: 'post',
		data: parametros
	})
	.done(function (rta_txt) {
		fnjs_submit('#movimiento','-');
	});
}

fnjs_submit=function(formulario,mov){

	$('#mov').val(mov);
	
	$(formulario).attr('action',"<?= $url_sincro_ver ?>");
  	fnjs_enviar_formulario(formulario);
}
</script>

<h3><?= _("personas en listas Madrid") ?></h3>
<?php
if (empty($mov)) {
	echo sprintf(_("unidas automáticamente: %s"),$cont_sync);
	echo '<br>';
	echo '<br>';
}
?>
<?php if (!empty($persona_listas)) { ?>
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
	echo "<td>".$persona_listas['id_nom_listas'].'<td>';
	echo "<td class='titulo'>".$persona_listas['ape_nom'].'<td>';
	echo "<td>".$persona_listas['nombre'].'<td>';
	echo "<td>".$persona_listas['apellido1'].'<td>';
	echo "<td>".$persona_listas['apellido2'].'<td>';
	echo "<td class='titulo'>".$persona_listas['f_nacimiento'].'<td>';
	echo '</tr>';
?>
</table>
<?php } ?>

<?php if (!empty($a_lista_orbix)) { ?>
<h3><?= _("posibles coincidencias") ?>:</h3>
<table>
<?php
foreach ($a_lista_orbix as $persona_orbix) {
	$id_orbix = $persona_orbix['id_nom'];
	echo "<tr>";
	echo "<td>".$persona_orbix['id_nom'].'<td>';
	echo "<td class='contenido'>".$persona_orbix['ape_nom'].'<td>';
	echo "<td>".$persona_orbix['nombre'].'<td>';
	echo "<td>".$persona_orbix['apellido1'].'<td>';
	echo "<td>".$persona_orbix['apellido2'].'<td>';
	echo "<td class='contenido'>".$persona_orbix['f_nacimiento'].'<td>';
	echo "<td class='titulo'><span class=link onClick='fnjs_unir($id_orbix)'>" . _("unir") . '</span><td>';
	echo '</tr>';
}
?>
</table>
<?php } ?>
<?php if (!empty($a_lista_orbix_otradl)) { ?>
<h3><?= _("posibles coincidencias en otras dl") ?>:</h3>
<table>
<?php
foreach ($a_lista_orbix_otradl as $persona_orbix) {
	$id_orbix = $persona_orbix['id_nom'];
	echo "<tr>";
	echo "<td>".$persona_orbix['id_nom'].'<td>';
	echo "<td class='contenido'>".$persona_orbix['ape_nom'].'<td>';
	echo "<td>".$persona_orbix['nombre'].'<td>';
	echo "<td>".$persona_orbix['apellido1'].'<td>';
	echo "<td>".$persona_orbix['apellido2'].'<td>';
	echo "<td class='contenido'>".$persona_orbix['f_nacimiento'].'<td>';
	echo "<td class='titulo'><span class=link onClick='fnjs_unir($id_orbix)'>" . _("unir") . '</span><td>';
	echo '</tr>';
}
?>
</table>
<?php } ?>
<?php if (!empty($persona_listas)) { ?>
<br>
<input type="button" value="<?= _("crear nuevo") ?>" onclick="fnjs_crear()">
</form>
<?php } ?>
