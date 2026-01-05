<?php


use core\ConfigGlobal;
use notas\model\getDatosActa;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\personas\domain\entity\Persona;


/**
* Esta página está como include de acta_2_mpdf.php
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		24/10/03.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
	include_once(ConfigGlobal::$dir_estilos.'/actas_mpdf.css.php'); 

// Crea los objetos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// conversion
$replace = src\configuracion\domain\entity\Config::$replace;
$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$nombre_prelatura = strtr("PRAELATURA SANCTAE CRUCIS ET OPERIS DEI", $replace);
$reg_stgr = "Stgr".ConfigGlobal::mi_region();

// acta
$ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
$oActa = $ActaRepository->findById($acta); // $acta está en el archivo que hace un include de este.
$id_asignatura = $oActa->getId_asignatura();
$id_activ = $oActa->getId_activ();
$oF_acta = $oActa->getF_acta();
$libro = $oActa->getLibro();
$pagina = $oActa->getPagina();
$linea = $oActa->getLinea();
$lugar = $oActa->getLugar();
$observ = $oActa->getObserv();

$oAsignatura = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class)->findById($id_asignatura);
if ($oAsignatura === null) {
    throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
}
$nombre_corto=$oAsignatura->getNombre_corto();
$nombre_asignatura = strtr($oAsignatura->getNombre_asignatura(), $replace);
$any=$oAsignatura->getYear();

$id_tipo=$oAsignatura->getId_tipo();
$oAsignaturaTipo = $GLOBALS['container']->get(AsignaturaTipoRepositoryInterface::class)->findById($id_tipo);
if ($oAsignatura === null) {
    throw new \Exception(sprintf(_("No se ha encontrado el tipo de asignatura con id: %s"), $id_tipo));
}
$curso = strtr($oAsignaturaTipo->getTipoLatinVo()->value(), $replace);

switch ($any) {
	case 1:
		$any="I";
		break;
	case 2:
		$any="II";
		break;
	case 3:
		$any="III";
		break;
	case 4:
		$any="IV";
		break;
	default:
		$any='';
}

// -----------------------------

$cPersonaNotas = getDatosActa::getNotasActa($acta);

// para ordenar
$errores = '';
$aPersonasNotas = [];
foreach($cPersonaNotas as $oPersonaNota) {
	$id_situacion=$oPersonaNota->getId_situacion();
	$id_nom=$oPersonaNota->getId_nom();
	$oPersona = Persona::findPersonaEnGlobal($id_nom);
	if ($oPersona === null) {
		$errores .= "<br>".sprintf(_("existe una nota de la que no se tiene acceso al nombre (id_nom = %s): es de otra dl o 'de paso' borrado."),$id_nom);
		$errores .= " " . _("no aparece en la lista");
		continue;
	}
	$nom = $oPersona->getApellidosUpperNombre();
		
	//$oNota = new notas\Nota($id_situacion);
	//$nota=$oNota->getDescripcion();
	$nota = $oPersonaNota->getNota_txt();
	$aPersonasNotas[$nom] = $nota;
}
uksort($aPersonasNotas, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.

$num_alumnos=count($aPersonasNotas);

// tribunal:
$ActaTribunalRepository = $GLOBALS['container']->get(ActaTribunalRepositoryInterface::class);
$cTribunal = $ActaTribunalRepository->getActasTribunales(array('acta'=>$acta,'_ordre'=>'orden'));
$num_examinadores=count($cTribunal);

// Definición del número de lineas de las páginas y los numeros de alumnos----------------
$lin_A4=42;										// número máximo de lineas en un A4
$lin_encabezado=16;								// número de lineas del encabezado asignatura + pie
$lin_encabezado_tribunal=4;						// número de lineas del encabezado tribunal
$lin_tribunal=$lin_encabezado_tribunal+2*$num_examinadores;  // número de lineas del tribunal

$lin_max_cara_A=$lin_A4 - $lin_encabezado - 2; 	// número máximo de lineas en la cara A 

if ($num_alumnos > $lin_max_cara_A) { $alum_cara_A=$lin_max_cara_A; } else { $alum_cara_A=$num_alumnos; }
$alum_cara_B=$num_alumnos-$alum_cara_A;

$cara='A';

$tribunal_html = "<div class=\"tribunal\">TRIBUNAL:</div>";
foreach ($cTribunal as $oTribunal) {
	$examinador=$oTribunal->getExaminador();
	$tribunal_html .= "<div class=\"examinador\">$examinador</div>";
}
$lugar_fecha = $lugar.",  ".$oF_acta->getFechaLatin();
$tribunal_html .= "<div class=\"fecha\">$lugar_fecha</div>";
$tribunal_html .= "<div class=\"sello\">L.S.<br>Studii Generalis</div>";

// ---------------------------------------------------------------------------------------
?>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<div class="A4" >
<?php if ($cara==="A") { ?>
<div class="cabecera"><?= $nombre_prelatura ?></div>
<div class="region">STUDIUM GENERALE REGIONIS: <?= $region_latin ?></div>
<div class="curso"><?= sprintf("CURSUS INSTITUTIONALES:&nbsp;&nbsp;  %s &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ANNUS: %s",$curso,$any); ?></div>
<div class="curso">DISCIPLINA: &nbsp;&nbsp;&nbsp;&nbsp;<?= $nombre_asignatura ?></div>
<div class="intro">Hisce litteris, quas propria uniuscuiusque subsignatione firmamus, fidem facimus hodierna die, coram infrascriptis Iudicibus, periculum de hac disciplina sequentes alumnos rite superasse:</div>
<table class="alumni" height="<?= $alum_cara_A ?>">
<tr><td width="55%" class="alumni">ALUMNI</td><td  width="10%">&nbsp;</td><td width="35%" class="alumni">CUM NOTA</td></tr>
<?php
	$i=0;
	foreach ($aPersonasNotas as $nom => $nota) {
		$i++;
		if ($i > $alum_cara_A) { continue;}
		?>
		<tr class="alumno">
		<td class="alumno"><?= $nom; ?>
		</td>
		<td>&nbsp;</td>
		<td class="nota"><?= $nota; ?></td>
		</tr>
		<?php
	}
	// linea final y linea de salto
	if ($num_alumnos>$alum_cara_A) {
		echo "<tr><td colspan=2 class=linea ><hr></td><td>(.../...)</td></tr>";
	} else {
		echo "<tr><td colspan=3 class=linea ><hr></td></tr>";
	}
	echo "</table>";

}

if ($cara==="A" && $num_alumnos+$lin_tribunal<$lin_max_cara_A) { $tribunal=1; }
if ($cara==="A" && $num_alumnos+$lin_tribunal>$lin_max_cara_A) { $tribunal=0; }

if (!empty($tribunal)){
	echo $tribunal_html; 
	$tribunal=0;
}

if ($cara==="A") {
?>
</div>
<div class="pie">
<div class="libro">
<b>Reg.</b> <?= $reg_stgr ?> &nbsp;
<b>lib.</b> <?= $libro; ?> &nbsp;
<b>pág.</b>  <?= $pagina; ?>
<b> n.</b> <?= $linea; ?>
</div>
<div class="acta">(N. <?= $acta; ?>)</div>
</div>
<div class="f7">F7</div>
<?php
}
$cara='B';

echo '<div class="A4" >';

if ($cara==="B" && $alum_cara_B > 0 ) {
	?>
	<table class="alumni" height="<?= $alum_cara_B ?>" >
	<tr><td width="55%" class="alumni"></td><td  width="10%"></td><td width="35%"></td></tr>
	<tr><td colspan="3">(.../...)<hr></td></tr>
	<?php
	$i = 0;
	foreach ($aPersonasNotas as $nom => $nota) {
		$i++;
		if ($i <= $lin_max_cara_A) { continue; }
		?>
		<tr class="alumno">
		<td class="alumno"><?= $nom; ?>
		</td>
		<td>&nbsp;</td>
		<td class="nota"><?= $nota; ?></td>
		</tr>
		<?php
	}
	// linea final y linea de salto
	echo "<tr><td colspan='3' class='linea' ><hr></td></tr>";
	echo "</tbody></table>";
}

// tribunal -----------------
if ($cara==="B" && $num_alumnos+$lin_tribunal>=$lin_max_cara_A) { $tribunal=1; }
if (!empty($tribunal)){
	echo $tribunal_html;
}
echo "</div>";