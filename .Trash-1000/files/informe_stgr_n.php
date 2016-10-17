<?php
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
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include ("./funciones_est.php");

/* Cálculo del informe cr 22/97. 
	Se coge a las personas que dependen de la dl en el momento actual (Fichero=A)
	Se incluyen los que estuvieron en la dl (Fecha cambio fichero entre 1/10/97 - FechaActual/98) y van a otra r, ci, (fichero= E(n),G(agd))
	Se crea una tabla temporal con estos alumnos: TempEstNumeraris
*/

/* Pongo en la variable $curs el periodo del curso */
$any=date("Y");
$any2= date("y");
$curs_ce= "bm,";
setType($curs_ce,"string");
$curs_ce = "'" . $curs_ce . "__-" . date("y") . "'";

$EstiuCurs=date("d/m/Y", mktime(0,0,0,7,2,$any)) ;
$inicurs= date("d/m/Y", mktime(0,0,0,10,1,$any-1)) ;
$fincurs= date("d/m/Y", mktime(0,0,0,9,30,$any)) ;
$curs="BETWEEN '$inicurs' AND '$fincurs' ";
$titulo_curso="Curso ".$any."-".($any-1);

//crear la tabla temporal de numerarios y notas
$tabla="tmp_est_numerarios";
$personas="p_numerarios";
$notas="tmp_notas_numerarios";
if (empty($_POST['lista'])) $_POST['lista']=0;
$pp=BorrarTablas($tabla,$personas,$notas,$_POST['lista']);


echo "<table border=1><thead>ALUMNOS N<br></thead>";

//1. Numerarios en Bienio
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla p
		WHERE p.stgr='b' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
$numB=$nf;
echo "<tr><th>1. </th><td>Número de numerarios en Bienio</td><td>$numB</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//2. Numerarios en año I de Cuadrienio
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla p
		WHERE p.stgr ='c1' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();

$numC1=$nf;
echo "<tr><th>2. </th><td>Numerarios en año I de Cuadrienio</td><td>$numC1</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//3. Numerarios en años II-IV de Cuadrienio. Cuento también los que han terminado este curso
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla p
		WHERE  p.stgr ='c2' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();

$numC2=$nf;
$numC=$numC1+$numC2; // sirve para el punto 8.
echo "<tr><th>3. </th><td>Numerarios en años II-IV de Cuadrienio</td><td>$numC2</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//4. Numerarios en Total
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.stgr
		FROM $tabla p
		WHERE p.stgr='b' OR p.stgr ILIKE 'c%'
		ORDER BY p.apellido1,p.apellido2,p.nom 
		";  
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>4. </th><td>Número de numerarios en stgr</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2,stgr",1); echo "</td></tr>"; }

//5. Numerarios del stgr sin la o
$ssql="SELECT p.nom, p.apellido1, p.apellido2
		FROM $tabla p
		WHERE p.f_fl IS NULL
			AND (p.stgr='b' OR p.stgr ILIKE 'c%') AND (p.f_o > '$EstiuCurs' OR p.f_o IS NULL)
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>5. </th><td>Numerarios del stgr sin la o</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//6. Numerarios en el ce
$ssql="SELECT p.nom, p.apellido1, p.apellido2
		FROM $tabla p
		WHERE (p.stgr='b' OR p.stgr ILIKE 'c%')
			AND ((p.lugar_ce='bm' AND p.fin_ce = '$any') OR p.vida_familia='n' OR p.vida_familia='m' OR p.vida_familia='k')
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
$nce=$nf;
echo "<tr><th>6. </th><td>Numerarios que estuvieron en el ce</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; 
	//6.1. Rendimiento de los alumnos de ce
	$ssql="SELECT count(*)
			FROM $tabla p, $notas n
			WHERE p.id_nom=n.id_nom 
				AND (n.id_nivel BETWEEN 1100 AND 1229 OR n.id_nivel BETWEEN 2100 AND 2429)
				AND ((p.lugar_ce='bm' AND p.fin_ce = '$any') OR p.vida_familia='n' OR p.vida_familia='m' OR p.vida_familia='k')
			 	AND (p.stgr='b' OR p.stgr ILIKE 'c%')
			"; 
	$oDBSt_sql=$oDB->query($ssql);
	SetType($nce,"double");
	$Aprobados=$oDBSt_sql->fetchColumn();
	if (!empty($nce)) { $nf=number_format(($Aprobados/$nce),2,',','.'); } else { $nf=0; }
	echo "<tr><td></td><td>6.1. Rendimiento de los alumnos del ce</td><td>$nf</td></tr>";
	echo "<tr><td></td><td>6.2. Total de asignaturas de B y C aprobadas por alumnos del ce</td><td> $Aprobados</td></tr>";
}
//7. Media de asignaturas superadas por alumno en bienio
$ssql="SELECT count(*)
		FROM $notas n
		WHERE (n.id_nivel BETWEEN 1100 AND 1232)
		 ";
$oDBSt_sql=$oDB->query($ssql);
SetType($numB,"double");
$AsigB=$oDBSt_sql->fetchColumn();
if (!empty($numB)) { $nf=number_format(($AsigB/$numB),2,',','.'); } else { $nf=0; }
echo "<tr><th>7. </th><td>Media de asignaturas superadas por alumno en bienio</td><td>$nf</td></tr>";
if (!empty($_POST['lista'])) echo "<tr><td></td><td>7.1 Total de asignaturas superadas en bienio</td><td> $AsigB</td></tr>";

//8. Media de asignaturas superadas por alumno en cuadrienio

//Miro que no exista nadie de repaso que haya cursado alguna asignatura
$ssql="SELECT p.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p,$notas n
		WHERE p.id_nom=n.id_nom
			AND (n.id_nivel BETWEEN 2100 AND 2500)
			AND p.stgr='r'
		GROUP BY p.id_nom, p.nom, p.apellido1, p.apellido2
		ORDER BY p.apellido1, p.apellido2, p.nom
		";
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
if ($nf >= 1){
	echo "<tr><td width=20><font color='Red'>&#161;OJO!</font></td>";
	echo "<tr><td></td><td>Hay $nf numerarios que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano</td></tr>";
	// Para sacar una lista
	echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; 
}

$ssql="SELECT count(*)
		FROM $notas n 
		WHERE n.id_nivel BETWEEN 2100 AND 2500
		 ";
$oDBSt_sql=$oDB->query($ssql);
SetType($numC,"double");
$AsigC=$oDBSt_sql->fetchColumn();
if (!empty($numC)) {$nf=number_format(($AsigC/$numC),2,',','.'); } else { $nf=0; }
echo "<tr><th>8. </th><td>Media de asignaturas superadas por alumno en cuadrienio</td><td>$nf</td></tr>";
if (!empty($_POST['lista'])) echo "<tr><td></td><td>8.1 Total de asignaturas superadas en cuadrienio</td><td> $AsigC</td></tr>";

//9. Número de numerarios de cuadrienio que han superado 1 curso (28.75 Creditos) 
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p,$notas n,xa_asignaturas a
		WHERE p.id_nom=n.id_nom AND n.id_asignatura=a.id_asignatura
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) >28.5
		ORDER BY p.apellido1,p.apellido2,p.nom  ";
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>9. </th><td>Número de numerarios de cuadrienio que han superado 1 curso</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//10. Número de numerarios de cuadrienio que han superado 1 semestre (14.25 Creditos) 
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p, $notas n, xa_asignaturas a
		WHERE p.id_nom=n.id_nom AND  n.id_nivel=a.id_nivel
			AND (p.stgr ILIKE 'c%' OR p.stgr='r')
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) > 14  
		ORDER BY p.apellido1,p.apellido2,p.nom  ";
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>10. </th><td>Número de numerarios de cuadrienio que han superado 1 semestre</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//11. Número de numerarios de cuadrienio que han superado menos de 1 semestre
$ssql="SELECT n.id_nom,p.nom, p.apellido1,p.apellido2
		FROM $tabla p, $notas n, xa_asignaturas a
		WHERE p.id_nom=n.id_nom AND  n.id_nivel=a.id_nivel
			AND (p.stgr ILIKE 'c%' OR p.stgr='r')
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom,p.nom, p.apellido1,p.apellido2
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) <=14  
		ORDER BY p.apellido1,p.apellido2,p.nom  ";
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>11. </th><td>Número de numerarios de cuadrienio que han superado menos de 1 semestre</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//12. Número de numerarios de cuadrienio que no han superado ninguna asignatura
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p LEFT JOIN $notas n USING (id_nom)
		WHERE p.stgr ~ '^c'
			AND n.id_nom IS NULL
		ORDER BY p.apellido1,p.apellido2,p.nom
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>12. </th><td>Número de numerarios de cuadrienio que no han superado ninguna asignatura </td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//13. Número de numerarios que han superado asignaturas con preceptor
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $notas n, $tabla p
		WHERE n.id_nom=p.id_nom AND n.preceptor='t' 
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2
		ORDER BY p.apellido1,p.apellido2,p.nom "; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>13. </th><td>Número de numerarios que han superado asignaturas con preceptor</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//14. Número de numerarios que han terminado el cuadrienio este curso 
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p, $notas n
		WHERE p.id_nom=n.id_nom
			AND (n.id_nivel=9998)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2
		ORDER BY p.apellido1, p.apellido2,p.nom"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>14. </th><td>Número de numerarios que han terminado el cuadrienio este curso </td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//15. Número de numerarios laicos con el cuadrienio terminado 
$ssql="SELECT p.id_nom,p.nom, p.apellido1, p.apellido2
	FROM $tabla p
	WHERE p.stgr='r' AND p.sacd='f'
 	ORDER BY p.apellido1, p.apellido2,p.nom"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>15. </th><td>Número de numerarios laicos con el cuadrienio terminado </td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

if (!empty($_POST['lista'])) {
	//xx. Numerarios que han terminado el ce este curso y con el bienio sin acabar 
	$sAsql="SELECT a.id_nivel, a.nombre_corto
	FROM xa_asignaturas a
	WHERE a.id_nivel BETWEEN 1100 AND 1300
	ORDER BY a.id_nivel"; 
	
	$ssql="SELECT p.id_nom, p.nom||' '||p.apellido1||' '||p.apellido2 as nom_ap, a.nombre_corto,a.id_nivel
	FROM $tabla p LEFT JOIN e_notas n USING (id_nom), xa_asignaturas a
	WHERE  p.fin_ce=$any2 AND p.lugar_ce = 'bm' AND p.stgr = 'b'
		AND n.id_nivel=a.id_nivel
		AND a.id_nivel BETWEEN 1100 AND 1300
	ORDER BY p.apellido1,p.apellido2,p.nom, a.id_nivel  "; 
	$oDBSt_Asql=$oDB->query($sAsql);
	$a_Asql=$oDBSt_Asql->fetchAll();
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	echo "<tr><td></td><td>xx. Numerarios que han terminado el ce este curso y con el bienio sin acabar: </td></tr>";
	// Para sacar una lista
	if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; ListaAsig($a_Asql,$oDBSt_sql); echo "</td></tr>"; }
	
	//xxx. Numerarios que han terminado el ce (otros anos) y con el bienio sin acabar 
	
	// mantengo la variable $Asql de arriba
	$ssql="SELECT p.id_nom, p.nom||' '||p.apellido1||' '||p.apellido2 as nom_ap, a.nombre_corto,a.id_nivel
	FROM  $tabla p LEFT JOIN e_notas n USING (id_nom),xa_asignaturas a
	WHERE p.fin_ce != $any2	AND p.stgr = 'b'
		AND n.id_nivel=a.id_nivel
		AND a.id_nivel BETWEEN 1100 AND 1300
	ORDER BY p.apellido1,p.apellido2,p.nom, a.id_nivel  "; 
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	echo "<tr><td></td><td>xxx. Numerarios que terminaron el ce en cursos anteriores y con el bienio sin acabar:</td></tr>";
	
	// Para sacar una lista
	if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; ListaAsig($a_Asql,$oDBSt_sql); echo "</td></tr>"; }
}
?>
</table>
