<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************



$tipo_persona = (string)  filter_input(INPUT_POST, 'tipo');	
$mi_dl = \core\ConfigGlobal::mi_dele();
if ($mi_dl == 'cr') {
	$dl = 'Hcr';
} else {
	$dl = substr($mi_dl, 2);
}

$id_tipo = 0;
switch ($tipo_persona) {
	case 'n':
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$id_tipo = 1;
			$obj_pau = 'GestorPersonaN';
		}
		break;
	case 'a':
		if ($_SESSION['oPerm']->have_perm("agd")) {
			$id_tipo = 2;
			$obj_pau = 'GestorPersonaAgd';
		}
		break;
	case 's':
		if ($_SESSION['oPerm']->have_perm("sg")) {
			$id_tipo = 3;
			$obj_pau = 'GestorPersonaS';
		}
		break;
}

if (empty($id_tipo)) {
	exit(_("No tiene permisos"));
}

//Orbix
$obj = 'personas\\model\\'.$obj_pau;
$GesPersonas = new $obj();

//listas
$Query = "SELECT * FROM dbo.q_dl_Estudios_b WHERE Dl='$dl' AND Identif LIKE '$id_tipo%'";
// todos los de listas
$oGesListas = new dbextern\model\GestorPersonaListas();	
$cPersonasListas = $oGesListas->getPersonaListasQuery($Query);
$i = 0;
$s = 0;
$a_ids_orbix = array();
foreach ($cPersonasListas as $oPersonaListas) {
	$id_nom_listas = $oPersonaListas->getIdentif();

	$oGesMatch = new dbextern\model\GestorIdMatchPersona();
	$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas'=>$id_nom_listas));
	if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
		//comprobar situación = 'A'
		$id_orbix = $cIdMatch[0]->getId_orbix();
		$cPersonas = $GesPersonas->getPersonasDl(array('id_nom' => $id_orbix));
		if (!empty($cPersonas) && count($cPersonas) > 0){
			$situacion = $cPersonas[0]->getSituacion();
			if ($situacion != 'A') {
				$a_ids_orbix[] = $cPersonas[0]->getId_nom();
				$s++;
			}
		}
		continue;
	}
	$i++;
}

$no_situacion = empty($s)? 0 : $s;
$ids_orbix_text = json_encode($a_ids_orbix);
$no_orbix = empty($i)? 0 : $i;

// todos los de orbix
$cPersonasOrbix = $GesPersonas->getPersonasDl(array('situacion'=>'A'));
$i = 0;
foreach ($cPersonasOrbix as $oPersonaOrbix) {
	$id_nom_orbix = $oPersonaOrbix->getId_nom();

	$oGesMatch = new dbextern\model\GestorIdMatchPersona();
	$cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix'=>$id_nom_orbix));
	if (!empty($cIdMatch[0]) AND count($cIdMatch) > 0) {
		continue;
	}
	$i++;
}
$no_listas = empty($i)? 0 : $i;

$ver_situacion = web\Hash::link('apps/dbextern/controller/sincro_ver_situacion.php?'.http_build_query(array('dl'=>$dl,'tipo_persona'=>$tipo_persona,'ids_orbix_text'=>$ids_orbix_text)));
$ver_listas = web\Hash::link('apps/dbextern/controller/sincro_ver.php?'.http_build_query(array('dl'=>$dl,'tipo_persona'=>$tipo_persona)));
$ver_orbix = web\Hash::link('apps/dbextern/controller/sincro_ver_orbix.php?'.http_build_query(array('dl'=>$dl,'tipo_persona'=>$tipo_persona)));

?>
<table>
	<tr><td>A.</td>
		<td> Personas en listas y en orbix, pero en situación incorrecta</td>
		<td><?= $no_situacion ?></td>
		<td><span class=link onclick="fnjs_update_div('#main','<?= $ver_situacion ?>')">ver</span></td>
	</tr>
	<tr><td>B.</td>
		<td> Personas en listas sin correspondencia en orbix</td>
		<td><?= $no_orbix ?></td>
		<td><span class=link onclick="fnjs_update_div('#main','<?= $ver_listas ?>')">ver</span></td>
	</tr>
	<tr><td>C.</td>
		<td> Personas en orbix sin correspondencia en listas</td>
		<td><?= $no_listas ?></td>
		<td><span class=link onclick="fnjs_update_div('#main','<?= $ver_orbix ?>')">ver</span></td>
	</tr>
</table>
	