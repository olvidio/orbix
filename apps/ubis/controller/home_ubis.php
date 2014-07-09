<?php
use ubis\model as ubis;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// si vengo por un listado...
empty($_POST['obj_pau'])? $obj_pau='' : $obj_pau=$_POST['obj_pau'];
if (!empty($_POST['id_ubi'])) {
	empty($_POST['id_ubi'])? $id_ubi="" : $id_ubi=$_POST['id_ubi'];
} elseif ($_POST['sel'][0]) {
	$id_ubi=$_POST['sel'][0];
	$id_ubi=strtok($id_ubi,'#');
} else { //si vengo por un go_to:
	if (!empty($_POST['atras'])) {
		$obj_pau = $oPosicion->getParametro('obj_pau');
		$id_ubi = $oPosicion->getParametro('id_ubi');
	}
}

// si vengo de los listados se scdl
if (isset($_SESSION['session_go_to']) ) {
	//$go_atras=$_SESSION['session_go_to']['sel']['go_atras'];
	$go_atras='';
} else {
	$go_atras='apps/ubis/controller/ubis_tabla.php';
}

$oUbi = ubis\Ubi::NewUbi($id_ubi);
$nombre_ubi=$oUbi->getNombre_ubi();
$dl=$oUbi->getDl();
$region=$oUbi->getRegion();
$tipo_ubi=$oUbi->getTipo_ubi();

$cDirecciones = $oUbi->getDirecciones();
$d = 0;
$direccion = '';
$poblacion = '';
$c_p = '';
$id_direccion = '';
foreach ($cDirecciones as $oDireccion) {
	$d++;
	if ($d > 1) {
		$direccion .= '<br>';
		$poblacion .= '<br>';
		$c_p .= '<br>';
		$id_direccion .= ',';
	}
	$direccion .= $oDireccion->getDireccion();
	$poblacion .= $oDireccion->getPoblacion();
	$c_p .= $oDireccion->getC_p();
	$id_direccion .= $oDireccion->getId_direccion();
}
$id_pau=$id_ubi;
$pau="u";

$mi_dele = core\ConfigGlobal::mi_dele();
switch ($tipo_ubi) {
	case "ctrdl":
		if ($dl != $mi_dele) {
			$obj_pau="Centro";
			$obj_dir="DireccionCtr";
		} else {
			$obj_pau="CentroDl";
			$obj_dir="DireccionCtrDl";
		}
		$ubi=_("centro");
		$tipo="ctr";
		break;
	case "ctrex":
		$obj_pau="CentroEx";
		$obj_dir="DireccionCtrEx";
		$ubi=_("centro");
		$tipo="ctr";
		break;
	case "cdcdl":
		if ($dl != $mi_dele) {
			$obj_pau="Casa";
			$obj_dir="DireccionCdc";
		} else {
			$obj_pau="CasaDl";
			$obj_dir="DireccionCdcDl";
		}
		$ubi=_("casa");
		$tipo="cdc";
		break;
	case "cdcex":
		$obj_pau="CasaEx";
		$obj_dir="DireccionCdcEx";
		$ubi=_("casa");
		$tipo="cdc";
		break;
	default:
		echo "tipo_ubi: $tipo_ubi<br>";
		print_r($_POST);
}

$gohome=web\Hash::link("apps/ubis/controller/home_ubis.php?id_ubi=$id_ubi&obj_pau=$obj_pau"); 
$godossiers=web\Hash::link("apps/dossiers/controller/dossiers_ver.php?pau=$pau&id_pau=$id_pau&obj_pau=$obj_pau&go_atras=$go_atras");

$go_ubi=web\Hash::link("apps/ubis/controller/ubis_editar.php?id_ubi=$id_ubi&obj_pau=$obj_pau");
$go_dir=web\Hash::link("apps/ubis/controller/direcciones_editar.php?id_ubi=$id_ubi&id_direccion=$id_direccion&obj_dir=$obj_dir"); 
$go_tel=web\Hash::link("apps/ubis/controller/teleco_tabla.php?id_ubi=$id_ubi&obj_pau=$obj_pau");

$alt=_("ver dossiers");
$dos=_("dossiers");
$txt=ucfirst(_("formato texto"));
$titulo=$nombre_ubi;

$telfs = $oUbi->getTeleco("telf","*"," / ") ;
$fax = $oUbi->getTeleco("fax","*"," / ") ;
$mails = $oUbi->getTeleco("e-mail","*"," / ") ;

echo $oPosicion->atras();
?>
<div id="top_ubis" name="top_ubis">
<table border=1><tr>
<?php if (core\ConfigGlobal::$ubicacion == 'int') { ?>
<td>
<span class=link onclick=fnjs_update_div('#main','<?= $godossiers ?>') ><img src=<?= core\ConfigGlobal::$web_icons ?>/dossiers.gif border=0 width=40 height=40 alt='<?= $alt ?>'>(<?= $dos ?>)</span>
</td>
<?php } ?>
<td class=titulo><span class=link onclick=fnjs_update_div('#main','<?= $gohome ?>')><?= $titulo ?></span></td>
<td><?= $dl ?>(<?= $region ?>)</td></tr>
<tr><td colspan=1><?= $direccion ?></td><td><?= $c_p ?></td><td><?= $poblacion ?></td></tr>
<tr><td colspan=4>telfs: <?= $telfs ?></td></tr>
<tr><td colspan=4>fax: <?= $fax ?></td></tr>
<tr><td colspan=4>e-mails: <?= $mails ?></td></tr>
<tr><th colspan=4><?= ucfirst(_("editar")) ?>: 
		&nbsp;<span class="link_inv" onclick="fnjs_update_div('#ficha_ubis','<?= $go_ubi ?>');"><?= $ubi ?></span>
		&nbsp;&nbsp;<span class="link_inv" onclick="fnjs_update_div('#ficha_ubis','<?= $go_dir ?>');"><?= _("dirección") ?></span>
		&nbsp;&nbsp;<span class="link_inv" onclick="fnjs_update_div('#ficha_ubis','<?= $go_tel ?>');"><?= _("telecos") ?></span>
		</th></tr>
</table>
</div>
<div id="ficha_ubis" name="ficha_ubis">
<?php
if (core\ConfigGlobal::$ubicacion == 'int') {
	include ("apps/dossiers/controller/lista_dossiers.php");
}
?>
</div>

