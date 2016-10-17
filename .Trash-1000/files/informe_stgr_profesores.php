<?php
/**
* Esta página sirve para el resumen anual de los profesores
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		23/3/2007
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


/* Pongo en la variable $curs el periodo del curso */
$any=date("Y");
$mes=date("m");

$inicurs= date("d/m/Y", mktime(0,0,0,10,1,$any-1)) ;
$fincurs= date("d/m/Y", mktime(0,0,0,9,30,$any)) ;
$curs="BETWEEN '$inicurs' AND '$fincurs' ";

if ($mes>9) {
	$any1=$any+1; 
	$curso_txt="$any-$any1";
} else { 
	$any1=$any-1;
	$curso_txt="$any1-$any";
}
echo "<p>$curso_txt</p>";

// posibles profesores (n y agd):
$sql_profes="CREATE TEMP TABLE profes AS SELECT id_nom FROM p_n_agd WHERE fichero='A' ORDER BY id_nom";
$oDBSt_q=$oDB->query($sql_profes);

// posibles profesores asociados (añado s y sss+):
$sql_profes_as="CREATE TEMP TABLE profes_as AS SELECT id_nom FROM p_de_casa WHERE fichero='A' ORDER BY id_nom";
$oDBSt_q=$oDB->query($sql_profes_as);

/* tipos de profesor:
	1 Ordinario
	2 Extraordinario
	3 Adjunto
	4 Encargado
	5 Ayudante
	6 Asociado
*/


// 32. nº de profesores ordinarios:
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes USING (id_nom) WHERE id_tipo_profesor=1 AND f_cese is null";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>32. Número de profesores ordinarios: $num</p>";


/*33. Número de profesores extraordinarios*/
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes USING (id_nom) WHERE id_tipo_profesor=2 AND f_cese is null";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>33. Número de profesores extraordinarios: $num</p>";

/*34. Número de profesores adjuntos*/
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes USING (id_nom) WHERE id_tipo_profesor=3 AND f_cese is null";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>34. Número de profesores adjuntos: $num</p>";

/*35. Número de profesores encargados*/
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes USING (id_nom) WHERE id_tipo_profesor=4 AND f_cese is null";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>35. Número de profesores encargados: $num</p>";

/*36. Número de profesores asociados*/
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes_as USING (id_nom) WHERE id_tipo_profesor=6 AND f_cese is null";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>36. Número de profesores asociados: $num</p>";

/*37. Número de profesores ayudantes*/
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes USING (id_nom) WHERE id_tipo_profesor=5 AND f_cese is null";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>37. Número de profesores ayudantes: $num</p>";

/*38. Número de total de profesores*/
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes_as USING (id_nom) WHERE f_cese is null";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>38. Número de total de profesores: $num</p>";

/*39. Número de profesores de latín*/
$sql="SELECT DISTINCT id_nom FROM d_profesor_latin JOIN profes_as USING (id_nom) WHERE latin='t'";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>39. Número de profesores de latín: $num</p>";

/*40. Número de profesores que dieron clase de su especialidad*/
$sql="SELECT DISTINCT id_nom,id_departamento FROM d_profesor_stgr JOIN profes_as USING (id_nom) WHERE f_cese is null";
//echo "sql: $sql<br>";
$oDBSt_q_sql=$oDB->query($sql);
$p=0;
$p_dep=0;
$p_no_dep=0;
foreach ($oDBSt_q_sql->fetchAll() as $row) {
	$p++;
	$id_nom=$row['id_nom'];
	$id_departamento=$row['id_departamento'];
	$sql="SELECT DISTINCT d.id_nom FROM d_docencia_stgr d JOIN xa_asignaturas a USING (id_asignatura), xe_sectores s
		WHERE s.id_departamento=$id_departamento AND a.id_sector=s.id_sector AND d.id_nom=$id_nom AND curso='$curso_txt' ";
	//echo "sql: $sql<br>";
	$oDBSt_q2_sql=$oDB->query($sql);
	$num=$oDBSt_q2_sql->rowCount();
	if (!empty($num)) {
		$p_dep++;
	} else {
		$sql3="SELECT DISTINCT d.id_nom FROM d_docencia_stgr d JOIN xa_asignaturas a USING (id_asignatura), xe_sectores s
			WHERE s.id_departamento!=$id_departamento AND a.id_sector=s.id_sector AND d.id_nom=$id_nom AND curso='$curso_txt' ";
		//echo "sql: $sql<br>";
		$oDBSt_q3_sql=$oDB->query($sql3);
		$num3=$oDBSt_q3_sql->rowCount();
		if (!empty($num3)) $p_no_dep++;
	}
}

echo "<p>40. Número de profesores que dieron clase de su especialidad: $p_dep</p>";
echo "<p>41. Número de profesores que dieron clase de otras asignaturas: $p_no_dep</p>";

/*42. Número de profesores asistentes a congresos...*/
$sql="SELECT DISTINCT id_nom FROM d_congresos JOIN profes USING (id_nom) WHERE f_ini $curs";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
echo "<p>42. Número de profesores asistentes a cve del stgr u otras reuniones: $num</p>";

/*43. Ratio alumno/profesor en bienio*/
//crear la tabla temporal de numerarios y notas
$tabla_n="tmp_est_numerarios";
$personas="p_numerarios";
$notas="tmp_notas_numerarios";
$pp=BorrarTablas($tabla_n,$personas,$notas);
//crear la tabla temporal de numerarios y notas
$tabla_a="tmp_est_agd";
$personas="p_agregados";
$notas="tmp_notas_agd";
$pp=BorrarTablas($tabla_a,$personas,$notas);

// Numerarios en Bienio
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla_n p
		WHERE p.stgr='b' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
$numB=$nf;
// Agregados en Bienio.
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla_a p
		WHERE p.stgr='b' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		";
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
$agdB=$nf;
// Profesores de Bienio.
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes USING (id_nom) WHERE f_cese is null AND id_departamento=1";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();

$ratioB=($numB+$agdB)/$num;

echo "<p>43. Ratio alumno/profesor en bienio: $ratioB</p>";

/*44. Ratio alumno/profesor en cuadrienio*/

// Numerarios en cuadrienio
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.stgr
		FROM $tabla_n p
		WHERE p.stgr ILIKE 'c%'
		ORDER BY p.apellido1,p.apellido2,p.nom 
		";  
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
$numC=$nf;
// Numerarios en cuadrienio
$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.stgr
		FROM $tabla_a p
		WHERE p.stgr ILIKE 'c%'
		ORDER BY p.apellido1,p.apellido2,p.nom 
		";  
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
$agdC=$nf;
// Profesores de Bienio.
$sql="SELECT DISTINCT id_nom FROM d_profesor_stgr JOIN profes USING (id_nom) WHERE f_cese is null AND id_departamento!=1";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();

$ratioC=($numC+$agdC)/$num;

echo "<p>44. Ratio alumno/profesor en cuadrienio: $ratioC</p>";
echo "<p>45. Nº de departamentos: 8</p>";


?>
</table>
</body>
</html>
