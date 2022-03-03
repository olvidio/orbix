<?php
use core\ConfigGlobal;
use notas\model as notas;
use ubis\model\entity\GestorDelegacion;

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

//por aproximación:
$a_ce = array('dlb'=>'Barcelona',
				'dlgr' => 'Granada',
				'dlmE' => 'Madrid (E)',
				'dlmO' => 'Madrid (O)',
				'dlp' => 'Pamplona (cep)',
				'dls' => 'Sevilla',
				'dln' => 'Valladolid',
				'dlal' => 'Valencia',
                'H' => 'región stgr H',
	);
$mi_dl = core\ConfigGlobal::mi_delef();
$ce_lugar = $a_ce[$mi_dl];
	
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

$Qdl = (array)  \filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
//crear la tabla temporal de numerarios y notas
$Qlista = (string) \filter_input(INPUT_POST, 'lista');
$lista = empty($Qlista)? false : true;

$Resumen = new notas\Resumen('numerarios');
if (!empty($Qdl)) {
	$region_stgr = ConfigGlobal::mi_dele();
	$gesDelegacion = new GestorDelegacion();
	$a_delegacionesStgr = $gesDelegacion->getArrayDlRegionStgr([$region_stgr]);
	$a_dl = [];
	foreach ($Qdl as $id_dl) {
		$a_dl[] = $a_delegacionesStgr[$id_dl];
	}
	$Resumen->setArrayDl($a_dl);
}
$Resumen->setAnyIniCurs($any_ini_curs);
$Resumen->setLista($lista);
$Resumen->nuevaTabla();

$Resumen->setCe_lugar($ce_lugar);

$res = [];
$a_textos = [];
// BIENIO
//1. Numerarios en el ce
$res[1] = $Resumen->enCe();
$a_textos[1] = ucfirst(_("numerarios en el ce"));
//2. Numerarios sin ce
$res[2] = $Resumen->sinCe();
$a_textos[2] = ucfirst(_("numerarios sin haber hecho el ce"));
//3. Numerarios que han terminado el ce este curso y con el bienio sin acabar 
//xxx. Numerarios que han terminado el ce (otros anos) y con el bienio sin acabar 
$res[3] = $Resumen->ceAcabadoEnBienio();
$a_textos[3] = ucfirst(_("numerarios que han terminado el ce (otros años) y con el bienio sin acabar"));
//4. Numerarios en Bienio
//$res[4] = $Resumen->enBienio(); Con los de ce es complicado, si tiene que ser la suma, mejor sumar:
$res[4]['num'] = $res[1]['num'] + $res[2]['num'] + $res[3]['num'];
$res[4]['lista'] = ucfirst(_("es la suma de los puntos: 1+2+3"));
$a_textos[4] = ucfirst(_("número de numerarios en Bienio"));
//5. Media de asignaturas superadas por alumno en ce
$nce = $res[1]['num'];
SetType($nce,"double");
$a_aprobadas = $Resumen->aprobadasCe();
$aprobadas = $a_aprobadas['num'];
if (!empty($nce)) { $nf=number_format(($aprobadas/$nce),2,',','.'); } else { $nf=0; }
$res[5]['num'] = $nf;
$a_textos[5] = ucfirst(_("media de asignaturas superadas por alumno en ce (n. 1)"));
//6. Media de asignaturas superadas por alumno sin haber hecho el ce
$nSince = $res[2]['num'];
SetType($nSince,"double");
$a_aprobadas_sin_ce = $Resumen->aprobadasSinCe();
$aprobadasSin = $a_aprobadas_sin_ce['num'];
if (!empty($nSince)) { $nf=number_format(($aprobadasSin/$nSince),2,',','.'); } else { $nf=0; }
$res[6]['num'] = $nf;
$a_textos[6] = ucfirst(_("media de asignaturas superadas por alumno sin haber hecho el ce (n. 2)"));
//7. Nº de n en bienio que han superado asignaturas con preceptor
$res[7] = $Resumen->conPreceptorBienio();
$a_textos[7] = ucfirst(_("nº de n en bienio que han superado asignaturas con preceptor"));
// 
// CUADRIENIO
//8. Numerarios en año I de Cuadrienio
$res[8] = $Resumen->enCuadrienio(1);
$a_textos[8] = ucfirst(_("número de numerarios en año I de Cuadrienio"));
//9. Numerarios en años II-IV de Cuadrienio. Cuento también los que han terminado este curso
$res[9] = $Resumen->enCuadrienio(2);
$a_textos[9] = ucfirst(_("número de numerarios en años II-IV de Cuadrienio"));
//10. Numerarios en Total
$res[10] = $Resumen->enCuadrienio('all');
$a_textos[10] = ucfirst(_("número de numerarios en cuadrienio"));
//11. Media de asignaturas superadas por alumno en cuadrienio
$a_aprobadas = $Resumen->aprobadasCuadrienio();
if (!isset($a_aprobadas['error'])) {
	$aprobadas = $a_aprobadas['num'];
	$numC = $res[10]['num'];
	SetType($numC,"double");
	if (!empty($numC)) { $nf=number_format(($aprobadas/$numC),2,',','.'); } else { $nf=0; }
	$res[11]['num'] = $nf;
	$res[11]['lista'] = $a_aprobadas['lista'];
	$a_textos[11] = ucfirst(_("media de asignaturas superadas por alumno en cuadrienio"));
} else {
	$res[11] = $a_aprobadas;
	$a_textos[11] = sprintf(_("ERROR: hay %s numerarios que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano"),$a_aprobadas['num']);
}
//12. Número de numerarios de cuadrienio que han superado 1 curso (28.75 Creditos) 
$res[12] = $Resumen->masAsignaturasQue(10);
$a_textos[12] = ucfirst(_("número de numerarios de cuadrienio que han superado 1 curso"));
//13. Número de numerarios de cuadrienio que han superado 1 semestre (14.25 Creditos) 
$res[13] = $Resumen->masAsignaturasQue(5);
$a_textos[13] = ucfirst(_("número de numerarios de cuadrienio que han superado 1 semestre"));
//14. Número de numerarios de cuadrienio que han superado menos de 1 semestre
$res[14] = $Resumen->menosAsignaturasQue(5);
$a_textos[14] = ucfirst(_("número de numerarios de cuadrienio que han superado menos de 1 semestre"));
//15. Número de numerarios de cuadrienio que no han superado ninguna asignatura
$res[15] = $Resumen->ningunaSuperada();
$a_textos[15] = ucfirst(_("número de numerarios de cuadrienio que no han superado ninguna asignatura"));
//16. Número de numerarios que han superado asignaturas con preceptor
$res[16] = $Resumen->conPreceptorCuadrienio();
$a_textos[16] = ucfirst(_("número de numerarios que han superado asignaturas con preceptor"));
//17. Número de numerarios que han terminado el cuadrienio este curso 
$res[17] = $Resumen->terminadoCuadrienio();
$a_textos[17] = ucfirst(_("número de numerarios que han terminado el cuadrienio este curso"));
// 
// REPASO
// 
//18. Número de numerarios laicos con el cuadrienio terminado 
$res[18] = $Resumen->laicosConCuadrienio();
$a_textos[18] = ucfirst(_("número de numerarios laicos con el cuadrienio terminado"));

if ($lista) {
	//x. Número de numerarios de repaso
	$res['x'] = $Resumen->enRepaso();
	$a_textos['x'] = ucfirst(_("número de numerarios de repaso"));
}

// ---------------------------------- html ----------------------------------------------------
?>
<script>
	fnjs_left_side_hide();
</script>
<p><?= \core\strtoupper_dlb(_("Alumnos numerarios")) ?>   <?= $curso_txt ?></p)>
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
