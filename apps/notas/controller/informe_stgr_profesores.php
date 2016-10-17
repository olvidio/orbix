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

/* tipos de profesor:
	1 Ordinario
	2 Extraordinario
	3 Adjunto
	4 Encargado
	5 Ayudante
	6 Asociado
*/
$Resumen = new notas\Resumen('profesores');
$Resumen->nuevaTablaProfe();

//32. nº de profesores ordinarios:
$res[32] = $Resumen->profesorDeTipo(1);
$a_textos[32] = _("Número de profesores ordinarios");
//33. Número de profesores extraordinarios
$res[33] = $Resumen->profesorDeTipo(2);
$a_textos[33] = _("Número de profesores extraordinarios");
//34. Número de profesores adjuntos
$res[34] = $Resumen->profesorDeTipo(3);
$res[34] = _("Número de profesores adjuntos");
//35. Número de profesores encargados
$res[35] = $Resumen->profesorDeTipo(4);
$res[35] = _("Número de profesores encargados");
//36. Número de profesores asociados
$res[36] = $Resumen->profesorDeTipo(6);
$res[36] = _("Número de profesores asociados");
//37. Número de profesores ayudantes
$res[37] = $Resumen->profesorDeTipo(5);
$res[37] = _("Número de profesores ayudantes");
//38. Número de total de profesores
$res[38] = $Resumen->profesorDeTipo(0);
$res[38] = _("Número de total de profesores");
//39. Número de profesores de latín
$res[39] = $Resumen->profesorDeLatin();
$res[39] = _("Número de profesores de latín");

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

$res[40] = _("Número de profesores que dieron clase de su especialidad");
echo "<p>40. Número de profesores que dieron clase de su especialidad: $p_dep</p>";
echo "<p>41. Número de profesores que dieron clase de otras asignaturas: $p_no_dep</p>";

/*42. Número de profesores asistentes a congresos...*/
$sql="SELECT DISTINCT id_nom FROM d_congresos JOIN profes USING (id_nom) WHERE f_ini $curs";

$oDBSt_q_sql=$oDB->query($sql);
$num=$oDBSt_q_sql->rowCount();
$res[42] = _("Número de profesores asistentes a cve del stgr u otras reuniones");

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

$res[43] = _("Ratio alumno/profesor en bienio");

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

$res[44] = _("Ratio alumno/profesor en cuadrienio");
$res[45] = _("Nº de departamentos");



// ---------------------------------- html ----------------------------------------------------

echo "<table border=1><thead>ALUMNOS N<br></thead>";

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
