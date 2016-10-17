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
$tabla="tmp_est_agd";
$personas="p_agregados";
$notas="tmp_notas_agd";
if (empty($_POST['lista'])) $_POST['lista']=0;
$pp=BorrarTablas($tabla,$personas,$notas,$_POST['lista']);


echo "<table border=1><thead>ALUMNOS AGD<br></thead>";

//18. Agregados en Bienio.
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla p
		WHERE p.stgr='b' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
$numB=$nf;
echo "<tr><th>18. </th><td>Número de Agregados en Bienio</td><td>$numB</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//19. Agregados en Cuadrienio.
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla p
		WHERE  p.stgr ILIKE 'c%'
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();

$numC=$nf;
echo "<tr><th>19. </th><td>Número de agregados en Cuadrienio</td><td>$numC</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//20. Agragados en Total
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,stgr
		FROM $tabla p
		WHERE p.stgr='b' OR p.stgr ILIKE 'c%'
		ORDER BY p.apellido1,p.apellido2,p.nom 
		";  
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>20. </th><td>Número de agregados en stgr</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2,stgr",1); echo "</td></tr>"; }

//21. Media de asignaturas superadas por alumno en bienio
$ssql="SELECT count(*)
		FROM $notas n
		WHERE (n.id_nivel BETWEEN 1100 AND 1232)
		 ";
$oDBSt_sql=$oDB->query($ssql);
SetType($numB,"double");
$AsigB=$oDBSt_sql->fetchColumn();
if (!empty($numB)) { $nf=number_format(($AsigB/$numB),2,',','.'); } else { $nf=0; }
echo "<tr><th>21. </th><td>Media de asignaturas superadas por alumno en bienio</td><td>$nf</td></tr>";
if (!empty($_POST['lista'])) echo "<tr><td></td><td>21.1 Total de asignaturas superadas en bienio</td><td>$AsigB</td></tr>";

//22. Media de asignaturas superadas por alumno en cuadrienio

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
	echo "<td>Hay $nf agregados que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano</td></tr>";
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
echo "<tr><th>22. </th><td>Media de asignaturas superadas por alumno en cuadrienio</td><td>$nf</td></tr>";
if (!empty($_POST['lista'])) echo "<tr><td></td><td>22.1 Total de asignaturas superadas en cuadrienio</td><td>$AsigC</td></tr>";

//23. Número de agregados de cuadrienio que han superado 1 curso (28.75 créditos)
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p,$notas n,xa_asignaturas a
		WHERE p.id_nom=n.id_nom AND n.id_asignatura=a.id_asignatura
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) >28.5
		ORDER BY p.apellido1,p.apellido2,p.nom  ";
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>23. </th><td>Número de agregados de cuadrienio que han superado 1 curso</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//24. Número de agregados de cuadrienio que han superado 1 semestre (14.25 Creditos) 
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
echo "<tr><th>24. </th><td>Número de agregados de cuadrienio que han superado 1 semestre</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//25. Número de agregados de cuadrienio que han superado menos de 1 semestre
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
echo "<tr><th>25. </th><td>Número de agregados de cuadrienio que han superado menos de 1 semestre</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>"; }

//26. Número de agregados de cuadrienio que no han superado ninguna asignatura
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p LEFT JOIN $notas n USING (id_nom)
		WHERE p.stgr ~ '^c'
			AND n.id_nom IS NULL
		ORDER BY p.apellido1,p.apellido2,p.nom
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>26. </th><td>Número de agregados de cuadrienio que no han superado ninguna asignatura </td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>";  }

//27. Número de agregados que han superado asignaturas con preceptor
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $notas n, $tabla p
		WHERE n.id_nom=p.id_nom AND n.preceptor='t' 
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2
		ORDER BY p.apellido1,p.apellido2,p.nom "; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>27. </th><td>Número de agregados que han superado asignaturas con preceptor</td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>";  }

//28. Número de agregados que han terminado el cuadrienio este curso 
$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2
		FROM $tabla p, $notas n
		WHERE p.id_nom=n.id_nom
			AND (n.id_nivel=9998)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2
		ORDER BY p.apellido1, p.apellido2,p.nom"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>28. </th><td>Número de agregados que han terminado el cuadrienio este curso </td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>";  }

//29. Número de agregados laicos con el cuadrienio terminado 
$ssql="SELECT p.id_nom,p.nom, p.apellido1, p.apellido2
	FROM $tabla p
	WHERE p.stgr='r' AND p.sacd='f'
 	ORDER BY p.apellido1, p.apellido2,p.nom"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
echo "<tr><th>29. </th><td>Número de agregados laicos con el cuadrienio terminado </td><td>$nf</td></tr>";
if ($_POST['lista'] && $nf >= 1){ echo "<tr><td colspan=3>"; Lista($ssql,"nom,apellido1,apellido2",1); echo "</td></tr>";  }

?>
</table>
