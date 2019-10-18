<?php
use asignaturas\model\entity as asignaturas;
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

$Qactualizar = (string) \filter_input(INPUT_POST, 'actualizar');
$Qid_tabla = (string) \filter_input(INPUT_POST, 'id_tabla');

if ($Qid_tabla == 'n') {
	$tabla = 'p_numerarios';
	$tabla_txt = 'Numerarios';
}
if ($Qid_tabla == 'a') {
	$tabla = 'p_agregados';
	$tabla_txt = 'Agregados';
}

$oDB = $GLOBALS['oDB'];

$superada = "(n.id_situacion = 10 OR n.id_situacion::text ~ '[1345]')";

if ($Qactualizar == 'c1') {
	$ssql="SELECT p.id_nom
		FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
		WHERE p.stgr != 'b' AND p.stgr !='c1'
			AND n.id_nivel BETWEEN 2100 AND 2113
		GROUP BY p.id_nom
		HAVING count(*) < 13 
		"; 
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$id_nom=$row["id_nom"];
		$ssql_1="UPDATE $tabla SET stgr='c1'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_1=$oDB->query($ssql_1);
	}
}
if ($Qactualizar == 'c2') {
	$ssql="SELECT p.id_nom
		FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
		WHERE p.stgr != 'b' AND p.stgr !='c2'
			AND n.id_nivel BETWEEN 2100 AND 2113
		GROUP BY p.id_nom
		HAVING count(*) > 12 
		"; 
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	$i=0;
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$i++;
		$id_nom=$row["id_nom"];
		$ssql_1="UPDATE $tabla SET stgr='c2'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_1=$oDB->query($ssql_1);
	}
}
if ($Qactualizar == 'r') {
	$ssql="SELECT p.id_nom
		FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
		WHERE p.stgr != 'r' AND n.id_asignatura = 9998 
		"; 
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	$i=0;
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$i++;
		$id_nom=$row["id_nom"];
		$ssql_1="UPDATE $tabla SET stgr='r'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_1=$oDB->query($ssql_1);
	}
}
if ($Qactualizar=="9999") {
	$ssql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*),stgr
		FROM $tabla p,e_notas_dl n
		WHERE p.id_nom=n.id_nom AND $superada
			AND (n.id_nivel BETWEEN 1000 AND 2000 OR id_nivel=9999)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,stgr
		HAVING count(*) >= 28 AND Max(n.id_nivel)<>9999
		ORDER BY p.apellido1 ASC,p.apellido2 ";
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	$i=0;
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$i++;
		$id_nom=$row["id_nom"];
		//busco su ultima fecha acta
		$ssql_1= "SELECT f_acta FROM e_notas_dl 
				WHERE id_nom=$id_nom and id_nivel between 1000 and 2000 
					AND f_acta is not null
				ORDER BY f_acta DESC";
		//echo "f_actas: $ssql_1<br>";
		$oDBSt_sql_1=$oDB->query($ssql_1);
		$f_acta=$oDBSt_sql_1->fetchColumn();
		if (empty($f_acta)) {
			//pongo la de hoy. creo que actualmente no se utiliza.
		    $oHoy = new web\DateTimeLocal();
		    $f_acta = $oHoy->getFromLocal();
		}
		
		$ssql_2="UPDATE $tabla SET stgr='c1'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_2=$oDB->query($ssql_2);
		
		$ssql_3="INSERT INTO e_notas_dl(id_nom,id_nivel,id_asignatura,f_acta,id_situacion,acta)
						 VALUES ($id_nom,'9999','9999','$f_acta',1,'fin bienio') ";
		$oDBSt_sql_3=$oDB->query($ssql_3);
	}
}
if ($Qactualizar=="9998") {
	$ssql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*),stgr
		FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
		WHERE $superada
			AND (n.id_nivel BETWEEN 2100 AND 2500 OR n.id_nivel=9998)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,stgr
		HAVING count(*) >= 53 AND Max(n.id_nivel)<>9998
		ORDER BY p.apellido1,p.apellido2,nom ";
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	$i=0;
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$i++;
		$id_nom=$row["id_nom"];
		//busco su ultima fecha acta
		$ssql_1= "SELECT f_acta FROM e_notas_dl
			WHERE id_nom=$id_nom and id_nivel between 2000 and 3000 
				AND f_acta is not null
			ORDER BY f_acta DESC";
		$oDBSt_sql_1=$oDB->query($ssql_1);
		$f_acta=$oDBSt_sql_1->fetchColumn();
		
		$ssql_2="UPDATE $tabla SET stgr='r'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_2=$oDB->query($ssql_2);
		
		$ssql_3="INSERT INTO e_notas_dl(id_nom,id_nivel,id_asignatura,f_acta,id_situacion,acta)
						 VALUES ($id_nom,'9998','9998','$f_acta',1,'fin cuadrienio') ";
		$oDBSt_sql_3=$oDB->query($ssql_3);
	}
}
?>
<html>
<head><style type="text/css">
 p {background-color: darkgray;} 
 p.action {background-color: lightgray;} 
 </style></head>
<body topmargin="-0,5cm" background=/icons/fons.gif link=#0000ff vlink=#0000ff>
<h2><center><font color=red>Comprobación del Fichero de Notas</font></center></h2>
<hr size="2" aling="center">
</bODY>
</html>

<?php
//0. Asegurar que el año de fin de ce está puesto con 4 cifras

/*1. Numerarios con el bienio terminado y sin poner que lo ha terminado */
$sql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*) as num_asig,stgr
FROM $tabla p,e_notas_dl n
WHERE p.id_nom=n.id_nom AND $superada
	AND (n.id_nivel BETWEEN 1000 AND 2000 OR n.id_nivel=9999)
GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,stgr
HAVING count(*) >= 28 AND Max(n.id_nivel)<>9999
ORDER BY p.apellido1 ASC,p.apellido2 ";

$oDBSt_bienio=$oDB->query($sql);
$nf=$oDBSt_bienio->rowCount();
echo "<p>1. $tabla_txt con el bienio terminado y sin poner que lo ha terminado : $nf</p>";
echo "<p>Es importante poner bien la fecha en que lo ha terminado</p>";
if (!empty($nf)) {
	/* Para sacar una lista*/
	echo "<table>";
	foreach ($oDBSt_bienio->fetchAll() as $algo) {
		$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
		$numasig= $algo['num_asig'];
		$stgr=$algo['stgr'];
		echo "<tr><td width=20></td>";
		echo "<td>$nom</td><td>$numasig</td><td>$stgr</td></tr>";
	}
	echo "<tr><td colspan=7><hr>";
	echo "</table>";
	/* end lista */
	$go=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>$Qid_tabla,'actualizar'=>9999)));
	$pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">". _("clic aquí") ."</span>";
	echo "<p class=action>";
	printf (_("para poner c1 y bienio finalizado a todos los de la lista, hacer %s. Esto pondrá la fecha de acta última."),$pag);
	echo "</p>";
}

/*2. Numerarios con el cuadrienio terminado y sin poner que lo ha terminado */
$sql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*) as num_asig,stgr
		FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
		WHERE $superada
			AND (n.id_nivel BETWEEN 2100 AND 2500 OR n.id_nivel=9998)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,stgr
		HAVING count(*) >= 53 AND Max(n.id_nivel)<>9998
		ORDER BY p.apellido1,p.apellido2,nom ";
		
$oDBSt_cuadrienio=$oDB->query($sql);
$nf=$oDBSt_cuadrienio->rowCount();
echo "<br><p>2. $tabla_txt con el cuadrienio terminado y sin poner que lo ha terminado : $nf</p>";

if (!empty($nf)) {
	/* Para sacar una lista*/
	echo "<table>";
	foreach ($oDBSt_cuadrienio->fetchAll() as $algo) {
		$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
		$numasig= $algo['num_asig'];
		$stgr=$algo['stgr'];
		echo "<tr><td width=20></td>";
		echo "<td>$nom</td><td>$numasig</td><td>$stgr</td></tr>";
	}
	echo "<tr><td colspan=7><hr>";
	echo "</table>";
	/* end lista */
	$go=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>$Qid_tabla,'actualizar'=>9998)));
	$pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">". _("clic aquí") ."</span>";
	echo "<p class=action>";
	printf (_("para poner r y cuadrienio finalizado a todos los de la lista, hacer %s. Esto pondrá la fecha de acta última."),$pag);
	echo "</p>";
}

/*3. Gente con opcionales genéricas sin fecha o ref. a la opcional que es (no cuento las de la Ratio 89)*/

/*4. Gente sin fecha en acta (no cuento las de la Ratio 89)*/

$sqlF="SELECT  p.id_nom,p.nom, p.apellido1, p.apellido2, n.f_acta, n.id_asignatura
FROM $tabla p,e_notas_dl n
WHERE p.id_nom=n.id_nom AND (n.f_acta) IS NULL AND (n.id_situacion = 10 OR n.id_situacion::text ~ '[34]')
ORDER BY p.apellido1,p.apellido2 ";

$oDBSt_sql=$oDB->query($sqlF);
$nf=$oDBSt_sql->rowCount();
echo "<br><p>4. $tabla_txt con asignaturas sin fecha de acta: $nf</p>";

/* Para sacar una lista*/
echo "<table>";
foreach ($oDBSt_sql->fetchAll() as $algo) {
	$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
	$fecha= $algo['f_acta'];
	$id_asignatura = $algo['id_asignatura'];
	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$asig= $oAsignatura->getNombre_corto();
	echo "<tr><td width=20></td>";
	echo "<td>$nom</td><td>$fecha</td><td>$asig</td></tr>";
}
echo "<tr><td colspan=7><hr>";
echo "</table>";
/* end lista */

// 5. Comprobar que los de año I tienen puesto c1
$ssql="SELECT p.stgr,p.nom, p.apellido1, p.apellido2, count(*) AS NumAsig
	FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
	WHERE p.stgr != 'b' AND p.stgr != 'r' AND p.stgr !='c1'
		AND ((n.id_nivel BETWEEN 2100 AND 2113) OR n.id_nivel=2430)
	GROUP BY p.id_nom,p.stgr,p.nom, p.apellido1, p.apellido2
	HAVING count(*) < 14 
	ORDER BY apellido1,apellido2,nom"; 

$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
if (!empty($nf)) {
	echo "<br><p>5. $tabla_txt con \"c1\" mal puesto: $nf</p>";
	// Para sacar una lista
	echo "<table>";
	foreach ($oDBSt_sql->fetchAll() as $algo) {
		$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
		$stgr= $algo['stgr'];
		$asig = $algo['numasig'];
		echo "<tr><td width=20></td>";
		echo "<td>$nom</td><td>$stgr</td><td>$asig</td></tr>";
	}
	echo "<tr><td colspan=7><hr>";
	echo "</table>";
	$go=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>$Qid_tabla,'actualizar'=>'c1')));
	$pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">". _("clic aquí") ."</span>";
	echo "<p class=action>";
	printf (_("para poner c1 a todos los de la lista, hacer %s"),$pag);
	echo "</p>";
}

// 6. Comprobar que los de año II-IV tienen puesto c2
$ssql="SELECT p.stgr,p.nom, p.apellido1, p.apellido2, count(*) AS NumAsig
	FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
	WHERE p.stgr != 'b' AND p.stgr != 'r' AND p.stgr !='c2'
		AND ((n.id_nivel BETWEEN 2100 AND 2113) OR n.id_nivel=2430)
	GROUP BY p.id_nom,p.stgr,p.nom, p.apellido1, p.apellido2
	HAVING count(*) > 13 
	ORDER BY apellido1,apellido2,nom"; 
	
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
if (!empty($nf)) {
	echo "<br><p>6. $tabla_txt con \"c2\" mal puesto: $nf</p>";
	// Para sacar una lista
	// Para sacar una lista
	echo "<table>";
	foreach ($oDBSt_sql->fetchAll() as $algo) {
		$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
		$stgr= $algo['stgr'];
		$asig = $algo['numasig'];
		echo "<tr><td width=20></td>";
		echo "<td>$nom</td><td>$stgr</td><td>$asig</td></tr>";
	}
	echo "<tr><td colspan=7><hr>";
	echo "</table>";
	$go=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>$Qid_tabla,'actualizar'=>'c2')));
	$pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">". _("clic aquí") ."</span>";

	echo "<p class=action>";
	printf (_("para poner c2 a todos los de la lista, hacer %s"),$pag);
	echo "</p>";
}

// 7. Comprobar que los han terminado tienen pueso r
$ssql="SELECT p.stgr,p.nom, p.apellido1, p.apellido2
	FROM $tabla p LEFT JOIN e_notas_dl n USING (id_nom)
	WHERE p.stgr != 'r' AND n.id_asignatura = 9998
	ORDER BY apellido1,apellido2,nom"; 
	
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
if (!empty($nf)) {
	echo "<br><p>7. $tabla_txt con \"r\" sin poner: $nf</p>";
	// Para sacar una lista
	// Para sacar una lista
	echo "<table>";
	foreach ($oDBSt_sql->fetchAll() as $algo) {
		$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
		$stgr= $algo['stgr'];
		echo "<tr><td width=20></td>";
		echo "<td>$nom</td><td>$stgr</td></tr>";
	}
	echo "<tr><td colspan=7><hr>";
	echo "</table>";
	$go=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/comprobar_notas.php?'.http_build_query(array('id_tabla'=>$Qid_tabla,'actualizar'=>'r')));
	$pag = "<span class=\"link\" onclick=\"fnjs_update_div('#main','$go');\">". _("clic aquí") ."</span>";

	echo "<p class=action>";
	printf (_("para poner c2 a todos los de la lista, hacer %s"),$pag);
	echo "</p>";
}


/*8. Gente con asignaturas cursadas sin aprobar*/
$sqlF="SELECT  p.id_nom,p.nom, p.apellido1, p.apellido2, n.f_acta, n.id_asignatura
FROM $tabla p,e_notas_dl n
WHERE p.id_nom=n.id_nom AND n.id_situacion = 2
ORDER BY p.apellido1,p.apellido2 ";

$oDBSt_sql=$oDB->query($sqlF);
$nf=$oDBSt_sql->rowCount();
echo "<br><p>8. $tabla_txt con asignaturas cursadas sin examinar: $nf</p>";

/* Para sacar una lista*/
echo "<table>";
foreach ($oDBSt_sql->fetchAll() as $algo) {
	$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
	$fecha= $algo['f_acta'];
	$id_asignatura = $algo['id_asignatura'];
	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$asig= $oAsignatura->getNombre_corto();
	echo "<tr><td width=20></td>";
	echo "<td>$nom</td><td>$fecha</td><td>$asig</td></tr>";
}
echo "<tr><td colspan=7><hr>";
echo "</table>";
/* end lista */

echo "</body>";
?>
