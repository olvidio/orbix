<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use src\configuracion\domain\value_objects\ConfigSnapshot;

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

require_once __DIR__ . '/../helpers/acta_imprimir_support.php';

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
include_once(OrbixRuntime::dirEstilos() . '/actas_mpdf.css.php');

$acta = acta_imprimir_acta_from_request();

$replace = OrbixRuntime::latinHtmlEntityReplaceMap();
$oConfig = $_SESSION['oConfig'] ?? null;
$region_latin = $oConfig instanceof ConfigSnapshot ? $oConfig->getNomRegionLatin() : '';
$nombre_prelatura = strtr('PRAELATURA SANCTAE CRUCIS ET OPERIS DEI', $replace);
$reg_stgr = 'Stgr' . OrbixRuntime::miRegion();

$payload = PostRequest::getDataFromUrl('/src/notas/acta_imprimir_presentacion_data', [
    'acta' => $acta,
    'mode' => 'mpdf',
]);
$presentacion = acta_imprimir_presentacion_from_payload($payload);
$aPersonasNotas = $presentacion['aPersonasNotas'];
$num_alumnos = $presentacion['num_alumnos'];
$lin_tribunal = $presentacion['lin_tribunal'];
$lin_max_cara_A = $presentacion['lin_max_cara_A'];
$alum_cara_A = $presentacion['alum_cara_A'];
$alum_cara_B = $presentacion['alum_cara_B'];
$curso = $presentacion['curso'];
$any = $presentacion['any'];
$nombre_asignatura = $presentacion['nombre_asignatura'];
$libro = $presentacion['libro'];
$pagina = $presentacion['pagina'];
$linea = $presentacion['linea'];
$tribunal_html = $presentacion['tribunal_html'];
$acta = $presentacion['acta'] !== '' ? $presentacion['acta'] : $acta;

// ---------------------------------------------------------------------------------------
?>
<meta charset="utf-8">
<div class="A4" >
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

$tribunal = 0;
if ($num_alumnos + $lin_tribunal < $lin_max_cara_A) {
    $tribunal = 1;
}
if ($num_alumnos + $lin_tribunal > $lin_max_cara_A) {
    $tribunal = 0;
}

if ($tribunal !== 0) {
	echo $tribunal_html;
}

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

echo '<div class="A4" >';

if ($alum_cara_B > 0) {
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
$tribunal = 0;
if ($num_alumnos + $lin_tribunal >= $lin_max_cara_A) {
    $tribunal = 1;
}
if ($tribunal !== 0) {
	echo $tribunal_html;
}
echo "</div>";
