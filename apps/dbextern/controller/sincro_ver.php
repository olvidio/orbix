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

$id = (string)  filter_input(INPUT_POST, 'id');
$mov = (string)  filter_input(INPUT_POST, 'mov');
//$tipo_persona = 'a';

$id_tipo = 0;
switch ($tipo_persona) {
	case 'n':
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$id_tipo = 1;
		}
		break;
	case 'a':
		if ($_SESSION['oPerm']->have_perm("agd")) {
			$id_tipo = 2;
		}
		break;
	case 's':
		if ($_SESSION['oPerm']->have_perm("sg")) {
			$id_tipo = 3;
		}
		break;
}

function syncro_automatico($a_persona_lista,$tipo_persona) {
	$id_nom_listas = $a_persona_lista['id_nom_listas'];
	$apellido1_sinprep = $a_persona_lista['apellido1_sinprep'];
	$apellido2_sinprep = $a_persona_lista['apellido2_sinprep'];
	$f_nacimiento = $a_persona_lista['f_nacimiento'];
	$nombre = $a_persona_lista['nombre'];
	
	$aWhere = array();
	$aOperador = array();
	$aWhere['id_tabla'] = $tipo_persona;
	$aWhere['apellido1'] = $apellido1_sinprep;
	$aWhere['apellido2'] = $apellido2_sinprep;
	$aWhere['f_nacimiento'] = "'$f_nacimiento'";
	$aWhere['nom'] = trim($nombre);

	$oGesPersonasDl = new personas\model\GestorPersonaDl();
	$cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere,$aOperador);
	if ($cPersonasDl !== false && count($cPersonasDl) == 1) {
		$oPersonaDl = $cPersonasDl[0];
		$id_nom = $oPersonaDl->getId_nom();

		$oIdMatch = new dbextern\model\IdMatchPersona($id_nom_listas);
		$oIdMatch->setId_orbix($id_nom);
		$oIdMatch->setId_tabla($tipo_persona);
		
		if ($oIdMatch->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
			print_r($oIdMatch);
			echo '<br>';
			return false;
		}
		return true;
	}
	return false;
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
	if (isset($_SESSION['DBListas'][$id])) {
		return $id;
	} else {
		return otro($id,$mov,$max);
	}
}


if (empty($id)) {
	$id=1;

	$Query = "SELECT * FROM dbo.q_dl_Estudios_b WHERE Dl='$dl' AND Identif LIKE '$id_tipo%'";
	//$Query = "SELECT * FROM dbo.q_dl_Estudios_b WHERE Dl='b' AND Identif LIKE '1%' ORDER BY ApeNom";
	///$Query = "select * from dbo.q_dl_Estudios_b where (ApeNom LIKE '%Cierva%') OR (ApeNom LIKE '%Busquets%') ORDER BY ApeNom";
	//$Query = "select * from dbo.q_dl_Estudios_b where Dl='b' AND Ctr='Raset'";
	//$Query = "SELECT * FROM dbo.q_dl_Estudios_b WHERE ctr='sgMontagut'";
	// todos los de listas
	$oGesListas = new dbextern\model\GestorPersonaListas();	
	$cPersonasListas = $oGesListas->getPersonaListasQuery($Query);
	$i = 0;
	$cont_sync = 0;
	foreach ($cPersonasListas as $oPersonaListas) {
		$id_nom_listas = $oPersonaListas->getIdentif();

		$oGesMatch = new dbextern\model\GestorIdMatchPersona();
		$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas'=>$id_nom_listas));
		if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
			continue;
		}
		$a_persona_lista ['id_nom_listas'] = $id_nom_listas;
		$a_persona_lista ['ape_nom'] = $oPersonaListas->getApeNom();
		$a_persona_lista ['nombre'] = $oPersonaListas->getNombre();
		$a_persona_lista ['apellido1'] = $oPersonaListas->getApellido1();
		$a_persona_lista ['apellido1_sinprep'] = $oPersonaListas->getApellido1_sinprep();
		$a_persona_lista ['apellido2'] = $oPersonaListas->getApellido2();
		$a_persona_lista ['apellido2_sinprep'] = $oPersonaListas->getApellido2_sinprep();
		$a_persona_lista ['f_nacimiento'] = $oPersonaListas->getFecha_Naci();
		// Sólo la primera vez (mov = ''):
		if (empty($mov) && syncro_automatico($a_persona_lista,$tipo_persona)) {
			$cont_sync++;
			continue;
		}

		// incremento antes para empezar en 1 y no en 0.
		$i++;
		$a_lista[$i] = $a_persona_lista;
	}
	$_SESSION['DBListas'] = $a_lista;
}

$max = count($_SESSION['DBListas'])-1;

$new_id = otro($id,$mov,$max);
// Buscar coincidentes en orix
$persona_listas = $_SESSION['DBListas'][$new_id];
$apellido1_sinprep = $persona_listas['apellido1_sinprep'];
$id_nom_listas = $persona_listas['id_nom_listas'];
$aWhere = array();
$aOperador = array();
$aWhere['id_tabla'] = $tipo_persona;
$aWhere['situacion'] = 'A';
$aWhere['apellido1'] = $apellido1_sinprep;
$aWhere['_ordre'] = 'apellido1, apellido2, nom';

$oGesPersonasDl = new personas\model\GestorPersonaDl();
$cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere,$aOperador);
$i = 0;
$a_lista_orbix = array();
foreach ($cPersonasDl as $oPersonaDl) {
	$id_nom = $oPersonaDl->getId_nom();
	$oGesMatch = new dbextern\model\GestorIdMatchPersona();
	$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix'=>$id_nom));
	if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
		continue;
	}
	$ape_nom = $oPersonaDl->getApellidosNombre();
	$nombre = $oPersonaDl->getNom();
	$apellido1 = $oPersonaDl->getApellido1();
	$apellido2 = $oPersonaDl->getApellido2();
	$f_nacimiento = empty($oPersonaDl->getF_nacimiento())? '??' : $oPersonaDl->getF_nacimiento();
	$a_lista_orbix[$i] = 	array('id_nom'=>$id_nom,
										'ape_nom'=>$ape_nom,
										'nombre'=>$nombre,
										'apellido1'=>$apellido1,
										'apellido2'=>$apellido2,
										'f_nacimiento'=>$f_nacimiento);
	$i++;
}

$url_sincro_ver = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ver.php';
$oHash = new web\Hash();
$oHash->setUrl($url_sincro_ver);
$oHash->setcamposNo('mov');
$a_camposHidden = array(
		'dl' => $dl,
		'tipo_persona' => $tipo_persona,
		'id' => $new_id,
		);
$oHash->setArraycamposHidden($a_camposHidden);

$url_sincro_ajax = core\ConfigGlobal::getWeb().'/apps/dbextern/controller/sincro_ajax.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url_sincro_ajax);
$oHash1->setCamposForm('que!id_nom_listas!id!id_orbix'); 
$h1 = $oHash1->linkSinVal();


$html_reg = sprintf(_("registro %s de %s"),$new_id,$max);
// ------------------ html ----------------------------------
?>
<script>
fnjs_sincronizar=function(){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=syncro&dl=<?= $dl ?>&tipo_persona=<?= $tipo_persona ?><?= $h1 ?>&PHPSESSID=<?php echo session_id(); ?>';
			 
	$.ajax({
		url: url,
		type: 'post',
		data: parametros,
		success: function (rta_txt) {
			//rta_txt=rta.responseText;
			//alert ('respuesta: '+rta_txt);
			fnjs_submit('#movimiento','-');
		}
	});
}

fnjs_crear=function(){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=crear&id=<?= $new_id?>&id_nom_listas=<?= $id_nom_listas ?><?= $h1 ?>&PHPSESSID=<?php echo session_id(); ?>';
			 
	$.ajax({
		url: url,
		type: 'post',
		data: parametros,
		success: function (rta_txt) {
			//rta_txt=rta.responseText;
			//alert ('respuesta: '+rta_txt);
			fnjs_submit('#movimiento','-');
		}
	});
}

fnjs_unir=function(id_orbix){
	var url='<?= $url_sincro_ajax ?>';
	var parametros='que=unir&id_orbix='+id_orbix+'&id=<?= $new_id?>&id_nom_listas=<?= $id_nom_listas ?><?= $h1 ?>&PHPSESSID=<?php echo session_id(); ?>';
			 
	$.ajax({
		url: url,
		type: 'post',
		data: parametros,
		success: function (rta_txt) {
			//rta_txt=rta.responseText;
			//alert ('respuesta: '+rta_txt);
			fnjs_submit('#movimiento','-');
		}
	});
}

fnjs_submit=function(formulario,mov){

	$('#mov').val(mov);
	
	$(formulario).attr('action',"<?= $url_sincro_ver ?>");
  	fnjs_enviar_formulario(formulario);
}
</script>

<h3><?= _("Personas en listas Madrid") ?></h3>
<?php
if (empty($mov)) {
	echo sprintf(_("unidas automáticamente: %s"),$cont_sync);
}
?>
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

<h3><?= _("Posibles Coincidencias") ?>:</h3>

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
	echo "<td class='titulo'><span class=link onClick='fnjs_unir($id_orbix)'>" . _("Unir") . '</span><td>';
	echo '</tr>';
}
?>
</table>
<br>
<input type="button" value="<?= _("crear nuevo") ?>" onclick="fnjs_crear()">
</form>
<br>
<input type="button" value="<?= _("actualizar datos de Listas a orbix") ?>" onclick="fnjs_sincronizar()">