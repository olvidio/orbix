<?php
use usuarios\model as usuarios;
use ubis\model as ubis;
/**
* Esta página muestra una tabla con los ubis seleccionados.
*
*
*@package	delegacion
*@subpackage	ubis
*@author	Daniel Serrabou
*@since		3/2/09.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv=core\ConfigGlobal::mi_sfsv();

$tabla="ubis";
$tabla_dir="u_direcciones";
$oDbl = $GLOBALS['oDBPC'];
if (!empty($_POST['nombre_ubi'])){
	$nom_ubi = str_replace("+", "\+", $_POST['nombre_ubi']); // para los centros de la sss+
	$aWhereCasa['nombre_ubi'] = $nom_ubi;
	$aOperadorCasa['nombre_ubi'] = 'sin_acentos';
	$aWhereCtr['nombre_ubi'] = $nom_ubi;
	$aOperadorCtr['nombre_ubi'] = 'sin_acentos';
}
switch ($miSfsv) {
	case 1:
		if (!($_SESSION['oPerm']->have_perm("vcsd") OR $_SESSION['oPerm']->have_perm("des"))) {
			$aWhereCasa['sv'] = 't';
		}
		break;
	case 2:
		$aWhereCasa['sf'] = 't';
	break;
}

$aWhereCasa['status'] = 't';
$aWhereCtr['status'] = 't';
$aWhereCtr['cdc'] = 't';

$a_ubis = array();
//Casas
$GesCasas = new ubis\GestorCasa();
$cCasas = $GesCasas->getCasas($aWhereCasa,$aOperadorCasa);
foreach ($cCasas as $oCasa) {
	$nombre_ubi = $oCasa->getNombre_ubi();
	$a_ubis[$nombre_ubi] = $oCasa;
}
//Ctrs
$GesCtr = new ubis\GestorCentro();
$cCtr = $GesCtr->getCentros($aWhereCtr,$aOperadorCtr);
foreach ($cCtr as $oCentro) {
	$nombre_ubi = $oCentro->getNombre_ubi();
	$a_ubis[$nombre_ubi] = $oCentro;
}

if (!($_SESSION['oPerm']->have_perm("vcsd") OR $_SESSION['oPerm']->have_perm("des"))) {
	//Ctrs sf
	$GesCtrSf = new ubis\GestorCentrosEllas();
	$cCtrSf = $GesCtrSf->getCentros($aWhereCtrSf,$aOperadorCtrSf);
	foreach ($cCtrSf as $oCentro) {
		$nombre_ubi = $oCentro->getNombre_ubi();
		$a_ubis[$nombre_ubi] = $oCentro;
	}
}


// oredenar los ubis
uksort($a_ubis, "strnatcasecmp"); // case insensitive


$a_cabeceras=array( ucfirst(_("nombre del centro")),
					_("tipo"),
					_("dl"),
					ucfirst(_("región")),
					ucfirst(_("dirección")),
					_("cp"),
					ucfirst(_("ciudad"))
				);

$i=0;
$a_valores = array();
foreach ($a_ubis as $oUbi) {
	$i++;
	$id_ubi = $oUbi->getId_ubi();
	$nom_ubi = $oUbi->getNombre_ubi();
	$tipo_ubi = substr($oUbi->getTipo_ubi(),0,3);

	$a_valores[$i]['sel'] = $id_ubi;
	$a_valores[$i][1] = $nom_ubi;
	$a_valores[$i][2] = $tipo_ubi;
	$a_valores[$i][3]=$oUbi->getDl();
	$a_valores[$i][4]=$oUbi->getRegion();

	$cDirecciones = $oUbi->getDirecciones();
	if (!empty($cDirecciones)) {
		$oDireccion = $cDirecciones[0];
		$direccion = $oDireccion->getDireccion();
		$c_p = $oDireccion->getC_p();
		$poblacion = $oDireccion->getPoblacion();
	} else {
		$direccion = '';
		$c_p = '';
		$poblacion = '';
	}
	$a_valores[$i][5] = $direccion;
	$a_valores[$i][6] = $c_p;
	$a_valores[$i][7] = $poblacion;

 }
// --------------------------------------- html --------------------------------------
?>
<table>
<?php
foreach($a_cabeceras as $cabecera) {
	echo "<th>$cabecera</th>";
}
echo "</tr>";
foreach($a_valores as $fila) {
		?>
		<tr><td class=link id='<?= $fila['sel'] ?>' onclick="fnjs_buscar('#frm_buscar_3','<?= $fila['sel'] ?>');" ><?= $fila[1] ?></td>
		<td><?= $fila[2] ?></td>
		<td><?= $fila[3] ?></td>
		<td><?= $fila[4] ?></td>
		<td><?= $fila[5] ?></td>
		<td><?= $fila[6] ?></td>
		<td><?= $fila[7] ?></td></tr>
		<?php
}
?>
