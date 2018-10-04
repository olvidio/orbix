<?php
use notas\model as notas;

/**
* Esta página sirve para comprobar las notas de la tabla e_notas.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		22/11/02.
*@version september 2018
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


/* Cálculo del informe cr 1/18. 
 * Se coge a las personas que dependen de la dl el 1 de octubre siguiente al curso al que se refiere el informe
 *	(Fichero=A)
 *	(Fichero=A y Fecha cambio fichero NULL o menor 2/10/18) + (Fichero != A y Fecha cambio fichero entre 1/10/18 - FechaActual/18)
 *	Se incluyen los que estuvieron en la dl, pero se han ido a otra r, a un ci, o se han ordenado.
 *  (si se han ordenado no influye??)
 *  (Fecha cambio fichero entre 1/10/17 - 1/10/18) y van a otra r, ci, (fichero= E(n),G(agd))
 *	Se crea una tabla temporal con estos alumnos: TempEstNumeraris
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
$Qlista = (string) \filter_input(INPUT_POST, 'lista');
$lista = empty($Qlista)? false : true;

$Resumen = new notas\Resumen('agregados');
$Resumen->setAnyIniCurs($any_ini_curs);
$Resumen->setLista($lista);
$Resumen->nuevaTabla();

// BIENIO
//21. Agregados en Bienio
$res[21] = $Resumen->enBienio();
$a_textos[21] = ucfirst(_("número de agregados en Bienio"));
//22. Media de asignaturas superadas por alumno en bienio
$nBienio = $res[21]['num'];
SetType($nBienio,"double");
$a_aprobadas = $Resumen->aprobadasBienio();
$aprobadas = $a_aprobadas['num'];
if (!empty($nce)) { $nf=number_format(($aprobadas/$nBienio),2,',','.'); } else { $nf=0; }
$res[22]['num'] = $nf;
$a_textos[22] = ucfirst(_("media de asignaturas superadas por alumno en bienio"));

//23 Nº de agd en cuadrienio con bienio pendiente
$res['23']['num'] = '?';
$a_textos['23'] = ucfirst(_("nº de agd en cuadrienio con bienio pendiente"));
//23.1 Nº de n en bienio que han superado asignaturas con preceptor
$res['23.1'] = $Resumen->conPreceptorBienio();
$a_textos['23.1'] = ucfirst(_("nº de n en bienio que han superado asignaturas con preceptor"));
// 
// CUADRIENIO
//24. Agregados en Cuadrienio
$res[24] = $Resumen->enCuadrienio('all');
$a_textos[24] = ucfirst(_("número de agregados Cuadrienio"));
//25. Media de asignaturas superadas por alumno en cuadrienio
$a_aprobadas = $Resumen->aprobadasCuadrienio();
if (!isset($a_aprobadas['error'])) {
	$aprobadas = $a_aprobadas['num'];
	$numC = $res[24]['num'];
	SetType($numC,"double");
	if (!empty($numC)) { $nf=number_format(($aprobadas/$numC),2,',','.'); } else { $nf=0; }
	$res[25]['num'] = $nf;
	$a_textos[25] = ucfirst(_("media de asignaturas superadas por alumno en cuadrienio"));
} else {
	$res[25] = $a_aprobadas;
	$a_textos[25] = sprintf(_("hay %s agregados que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano"),$a_aprobadas['num']);
}
//26. Número de agregados de cuadrienio que han superado 1 curso (28.75 Creditos) 
$res[26] = $Resumen->masCreditosQue('28.75');
$a_textos[26] = ucfirst(_("número de agregados de cuadrienio que han superado 1 curso"));
//27. Número de agregados de cuadrienio que han superado 1 semestre (14.25 Creditos) 
$res[27] = $Resumen->masCreditosQue('14');
$a_textos[27] = ucfirst(_("número de agregados de cuadrienio que han superado 1 semestre"));
//28. Número de agregados de cuadrienio que han superado menos de 1 semestre
$res[28] = $Resumen->menosCreditosQue('14');
$a_textos[28] = ucfirst(_("número de agregados de cuadrienio que han superado menos de 1 semestre"));
//29. Número de agregados de cuadrienio que no han superado ninguna asignatura
$res[29] = $Resumen->ningunaSuperada();
$a_textos[29] = ucfirst(_("número de agregados de cuadrienio que no han superado ninguna asignatura"));
//30. Número de agregados que han superado asignaturas con preceptor
$res[30] = $Resumen->conPreceptorCuadrienio();
$a_textos[30] = ucfirst(_("número de agregados que han superado asignaturas con preceptor"));
//31. Número de agregados que han terminado el cuadrienio este curso 
$res[31] = $Resumen->terminadoCuadrienio();
$a_textos[31] = ucfirst(_("número de agregados que han terminado el cuadrienio este curso"));
//32. Número total de alumnos agregados 
$res[32]['num'] = $res[21]['num'] + $res[24]['num'];
$a_textos[32] = ucfirst(_("número total de alumnos agregados"));

// 
// REPASO
// 
//33. Número de agregados laicos con el cuadrienio terminado 
$res[33] = $Resumen->laicosConCuadrienio();
$a_textos[33] = ucfirst(_("número de agregados laicos con el cuadrienio terminado"));

if ($lista) {
	//x. Número de numerarios de repaso
	$res['x'] = $Resumen->enRepaso();
	$a_textos['x'] = ucfirst(_("número de agregados de repaso"));
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
