<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

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

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
include_once(OrbixRuntime::dirEstilos() . '/actas_mpdf.css.php');

$replace = OrbixRuntime::latinHtmlEntityReplaceMap();
$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$nombre_prelatura = strtr('PRAELATURA SANCTAE CRUCIS ET OPERIS DEI', $replace);
$reg_stgr = 'Stgr' . OrbixRuntime::miRegion();

$d = PostRequest::getDataFromUrl('/src/notas/acta_imprimir_presentacion_data', [
    'acta' => $acta,
    'mode' => 'mpdf',
]);
$aPersonasNotas = [];
foreach ($d['aPersonasNotas_list'] ?? [] as $row) {
    $aPersonasNotas[$row['nom']] = $row['nota'];
}
$num_alumnos = (int)($d['num_alumnos'] ?? 0);
$lin_tribunal = (int)($d['lin_tribunal'] ?? 0);
$lin_max_cara_A = (int)($d['lin_max_cara_A'] ?? 0);
$alum_cara_A = (int)($d['alum_cara_A'] ?? 0);
$alum_cara_B = (int)($d['alum_cara_B'] ?? 0);
$curso = (string)($d['curso'] ?? '');
$any = (string)($d['any'] ?? '');
$nombre_asignatura = (string)($d['nombre_asignatura'] ?? '');
$libro = (string)($d['libro'] ?? '');
$pagina = (string)($d['pagina'] ?? '');
$linea = (string)($d['linea'] ?? '');
$tribunal_html = (string)($d['tribunal_html'] ?? '');

$cara = 'A';

// ---------------------------------------------------------------------------------------
?>
<meta charset="utf-8">
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