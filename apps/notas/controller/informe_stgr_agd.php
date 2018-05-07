<?php
use notas\model\entity as notas;

/**
* Esta página sirve para comprobar las notas de la tabla e_notas.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		22/11/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//include ("./funciones_est.php");

/* Cálculo del informe cr 22/97. 
	Se coge a las personas que dependen de la dl en el momento actual (Fichero=A)
	Se incluyen los que estuvieron en la dl (Fecha cambio fichero entre 1/10/97 - FechaActual/98) y van a otra r, ci, (fichero= E(n),G(agd))
	Se crea una tabla temporal con estos alumnos
*/

/* Pongo en la variable $curs el periodo del curso */
$any=date("Y");
$mes=date("m");
if ($mes>3) {
	$any1=$any-1; 
	$curso_txt="$any1-$any";
	$any_ini_curs = $any1;
} else { 
	$any1=$any-2;
	$any--;
	$curso_txt="$any1-$any";
	$any_ini_curs = $any1;
}
//crear la tabla temporal de agregados y notas
$lista = empty($_POST['lista'])? false : true;

$Resumen = new notas\Resumen('agregados');
$Resumen->setAnyIniCurs($any_ini_curs);
$Resumen->setLista($lista);
$Resumen->nuevaTabla();

//$Resumen->setCe_lugar('bm');

//18. Agregados en Bienio
$res[18] = $Resumen->enBienio();
$a_textos[18] = _("Número de agregados en Bienio");
//19. Agregados en año I de Cuadrienio
$res[19] = $Resumen->enCuadrienio('all');
$a_textos[19] = _("Número de agregados Cuadrienio");
//20. Agregados en Total
$res[20] = $Resumen->enTotal();
$a_textos[20] = _("Número de agregados en stgr");
//21. Media de asignaturas superadas por alumno en bienio
$a_aprobadas = $Resumen->aprobadasBienio();
$aprobadas = $a_aprobadas['num'];
$numB = $res[18]['num'];
SetType($numB,"double");
if (!empty($numB)) { $nf=number_format(($aprobadas/$numB),2,',','.'); } else { $nf=0; }
$res[21]['num'] = $nf;
$a_textos[21] = _("Media de asignaturas superadas por alumno en bienio");
//22. Media de asignaturas superadas por alumno en cuadrienio
$a_aprobadas = $Resumen->aprobadasCuadrienio();
if (!isset($a_aprobadas['error'])) {
	$aprobadas = $a_aprobadas['num'];
	$numC = $res[19]['num'];
	SetType($numC,"double");
	if (!empty($numC)) { $nf=number_format(($aprobadas/$numC),2,',','.'); } else { $nf=0; }
	$res[22]['num'] = $nf;
	$a_textos[22] = _("Media de asignaturas superadas por alumno en cuadrienio");
} else {
	$res[22] = $a_aprobadas;
	$a_textos[22] = sprintf(_("Hay %s agregados que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano"),$a_aprobadas['num']);

}
//23. Número de agregados de cuadrienio que han superado 1 curso (28.75 Creditos) 
$res[23] = $Resumen->masCreditosQue('28.75');
$a_textos[23] = _("Número de agregados de cuadrienio que han superado 1 curso");
//24. Número de agregados de cuadrienio que han superado 1 semestre (14.25 Creditos) 
$res[24] = $Resumen->masCreditosQue('14');
$a_textos[24] = _("Número de agregados de cuadrienio que han superado 1 semestre");
//25. Número de agregados de cuadrienio que han superado menos de 1 semestre
$res[25] = $Resumen->menosCreditosQue('14');
$a_textos[25] = _("Número de agregados de cuadrienio que han superado menos de 1 semestre");
//26. Número de agregados de cuadrienio que no han superado ninguna asignatura
$res[26] = $Resumen->ningunaSuperada();
$a_textos[26] = _("Número de agregados de cuadrienio que no han superado ninguna asignatura");
//27. Número de agregados que han superado asignaturas con preceptor
$res[27] = $Resumen->conPreceptor();
$a_textos[27] = _("Número de agregados que han superado asignaturas con preceptor");
//24. Número de agregados que han terminado el cuadrienio este curso 
$res[28] = $Resumen->terminadoCuadrienio();
$a_textos[28] = _("Número de agregados que han terminado el cuadrienio este curso");
//29. Número de agregados laicos con el cuadrienio terminado 
$res[29] = $Resumen->laicosConCuadrienio();
$a_textos[29] = _("Número de agregados laicos con el cuadrienio terminado");

if ($lista) {
	//x. Número de numerarios de repaso
	$res['x'] = $Resumen->enRepaso();
	$a_textos['x'] = _("Número de agregados de repaso");
}

// ---------------------------------- html ----------------------------------------------------

?>
<p><?= \core\strtoupper_dlb(_("alumnos agregados")) ?>   <?= $curso_txt ?></p>
<table border=1>
<?php
foreach ($res as $n => $datos) {
	$pos = strpos($n, ".");
	if ($pos !== false) {
		$tab = "<td></td><td>$n. ";
	} else {
		$tab = "<th>$n. </th><td>";
	}
	?>
	<tr><?= $tab ?><?= $a_textos[$n] ?></td><td><?= $datos['num'] ?></td></tr>
	<?php if (!empty($datos['lista'])){ ?>
	   <tr><td colspan=3>
		<?= $datos['lista']; ?>
	   </td></tr>
	<?php }
}
?>
</table>
