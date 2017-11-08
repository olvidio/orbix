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

//por aproximación:
$a_ce = array('dlb'=>'Barcelona',
				'dlgr' => 'Granada',
				'dlmE' => 'Madrid (E)',
				'dlmO' => 'Madrid (O)',
				'dlp' => 'Pamplona (cep)',
				'dls' => 'Sevilla',
				'dlst' => 'Santiago',
				'dlv' => 'Valladolid',
				'dlva' => 'Valencia',
				'dlz' => 'Zaragoza'
	);
$mi_dl = core\ConfigGlobal::mi_dele();
$ce_lugar = $a_ce[$mi_dl];
	
/* Cálculo del informe cr 22/97. 
	Se coge a las personas que dependen de la dl en el momento actual (Fichero=A)
	Se incluyen los que estuvieron en la dl (Fecha cambio fichero entre 1/10/97 - FechaActual/98) y van a otra r, ci, (fichero= E(n),G(agd))
	Se crea una tabla temporal con estos alumnos: TempEstNumeraris
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

//crear la tabla temporal de numerarios y notas
$lista = empty($_POST['lista'])? false : true;

$Resumen = new notas\Resumen('numerarios');
$Resumen->setAnyIniCurs($any_ini_curs);
$Resumen->setLista($lista);
$Resumen->nuevaTabla();

$Resumen->setCe_lugar($ce_lugar);

//1. Numerarios en Bienio
$res[1] = $Resumen->enBienio();
$a_textos[1] = _("Número de numerarios en Bienio");
//2. Numerarios en año I de Cuadrienio
$res[2] = $Resumen->enCuadrienio(1);
$a_textos[2] = _("Número de numerarios en año I de Cuadrienio");
//3. Numerarios en años II-IV de Cuadrienio. Cuento también los que han terminado este curso
$res[3] = $Resumen->enCuadrienio(2);
$a_textos[3] = _("Número de numerarios en años II-IV de Cuadrienio");
//4. Numerarios en Total
$res[4] = $Resumen->enTotal();
$a_textos[4] = _("Número de numerarios en stgr");
//5. Numerarios del stgr sin la o
$res[5] = $Resumen->enStgrSinO();
$a_textos[5] = _("Numerarios del stgr sin la o");
//6. Numerarios en el ce
$res[6] = $Resumen->finCe();
$a_textos[6] = _("Numerarios que estuvieron en el ce");
//6.1. Rendimiento de los alumnos del ce
$nce = $res[6]['num'];
SetType($nce,"double");
$a_aprobadas = $Resumen->aprobadasCe();
$aprobadas = $a_aprobadas['num'];
if (!empty($nce)) { $nf=number_format(($aprobadas/$nce),2,',','.'); } else { $nf=0; }
$res['6.1']['num'] = $nf;
$a_textos['6.1'] = _("Rendimiento de los alumnos del ce");
$res['6.2'] = $Resumen->aprobadasCe();
$a_textos['6.2'] = _("Total de asignaturas de B y C aprobadas por alumnos del ce");
//7. Media de asignaturas superadas por alumno en bienio
$a_aprobadas = $Resumen->aprobadasBienio();
$aprobadas = $a_aprobadas['num'];
$numB = $res[1]['num'];
SetType($numB,"double");
if (!empty($numB)) { $nf=number_format(($aprobadas/$numB),2,',','.'); } else { $nf=0; }
$res[7]['num'] = $nf;
$a_textos[7] = _("Media de asignaturas superadas por alumno en bienio");
//8. Media de asignaturas superadas por alumno en cuadrienio
$a_aprobadas = $Resumen->aprobadasCuadrienio();
if (!isset($a_aprobadas['error'])) {
	$aprobadas = $a_aprobadas['num'];
	$numC = $res[4]['num'];
	SetType($numC,"double");
	if (!empty($numC)) { $nf=number_format(($aprobadas/$numC),2,',','.'); } else { $nf=0; }
	$res[8]['num'] = $nf;
	$a_textos[8] = _("Media de asignaturas superadas por alumno en cuadrienio");
} else {
	$res[8] = $a_aprobadas;
	$a_textos[8] = sprintf(_("Hay %s numerarios que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano"),$a_aprobadas['num']);

}
//9. Número de numerarios de cuadrienio que han superado 1 curso (28.75 Creditos) 
$res[9] = $Resumen->masCreditosQue('28.75');
$a_textos[9] = _("Número de numerarios de cuadrienio que han superado 1 curso");
//10. Número de numerarios de cuadrienio que han superado 1 semestre (14.25 Creditos) 
$res[10] = $Resumen->masCreditosQue('14');
$a_textos[10] = _("Número de numerarios de cuadrienio que han superado 1 semestre");
//11. Número de numerarios de cuadrienio que han superado menos de 1 semestre
$res[11] = $Resumen->menosCreditosQue('14');
$a_textos[11] = _("Número de numerarios de cuadrienio que han superado menos de 1 semestre");
//12. Número de numerarios de cuadrienio que no han superado ninguna asignatura
$res[12] = $Resumen->ningunaSuperada();
$a_textos[12] = _("Número de numerarios de cuadrienio que no han superado ninguna asignatura");
//13. Número de numerarios que han superado asignaturas con preceptor
$res[13] = $Resumen->conPreceptor();
$a_textos[13] = _("Número de numerarios que han superado asignaturas con preceptor");
//14. Número de numerarios que han terminado el cuadrienio este curso 
$res[14] = $Resumen->terminadoCuadrienio();
$a_textos[14] = _("Número de numerarios que han terminado el cuadrienio este curso");
//15. Número de numerarios laicos con el cuadrienio terminado 
$res[15] = $Resumen->laicosConCuadrienio();
$a_textos[15] = _("Número de numerarios laicos con el cuadrienio terminado");

if ($lista) {
	//x. Número de numerarios de repaso
	$res['x'] = $Resumen->enRepaso();
	$a_textos['x'] = _("Número de numerarios de repaso");
}

if (!$lista) {
	//xx. Numerarios que han terminado el ce este curso y con el bienio sin acabar 
	$res['xx'] = $Resumen->bienioSinAcabar(1);
	$a_textos['xx'] = _("Numerarios que han terminado el ce este curso y con el bienio sin acabar");
	//xxx. Numerarios que han terminado el ce (otros anos) y con el bienio sin acabar 
	$res['xxx'] = $Resumen->bienioSinAcabar(0);
	$a_textos['xxx'] = _("Numerarios que han terminado el ce (otros años) y con el bienio sin acabar");
}


// ---------------------------------- html ----------------------------------------------------
?>
<p><?= \core\strtoupper_dlb(_("alumnos numerarios")) ?>   <?= $curso_txt ?></p>
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
