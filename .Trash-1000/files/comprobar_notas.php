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

$_POST['actualizar'] = empty($_POST['actualizar'])? '' : $_POST['actualizar'];

if ($_POST['actualizar']=="c1") {
	$tabla="p_n_agd";
	$ssql="SELECT p.id_nom
		FROM p_n_agd p LEFT JOIN e_notas n USING (id_nom)
		WHERE p.stgr != 'b' AND p.stgr !='c1'
			AND ((n.id_nivel BETWEEN 2100 AND 2113) OR n.id_nivel=2430)
		GROUP BY p.id_nom
		HAVING count(*) < 14 
		"; 
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$id_nom=$row["id_nom"];
		$ssql_1="UPDATE p_n_agd SET stgr='c1'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_1=$oDB->query($ssql_1);
	}
}
if ($_POST['actualizar']=="c2") {
	$tabla="p_n_agd";
	$ssql="SELECT p.id_nom
		FROM p_n_agd p LEFT JOIN e_notas n USING (id_nom)
		WHERE p.stgr != 'b' AND p.stgr !='c2'
			AND ((n.id_nivel BETWEEN 2100 AND 2113) OR n.id_nivel=2430)
		GROUP BY p.id_nom
		HAVING count(*) > 13 
		"; 
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	$i=0;
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$i++;
		$id_nom=$row["id_nom"];
		$ssql_1="UPDATE p_n_agd SET stgr='c2'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_1=$oDB->query($ssql_1);
	}
}
if ($_POST['actualizar']=="9999") {
	$tabla="p_n_agd";
	$ssql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*),stgr
		FROM p_n_agd p,e_notas n,e_notas_situacion s
		WHERE p.id_nom=n.id_nom AND n.id_situacion=s.id_situacion AND s.superada='t'
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
		$ssql_1= "SELECT f_acta FROM e_notas 
				WHERE id_nom=$id_nom and id_nivel between 1000 and 2000 
					AND f_acta is not null
				ORDER BY f_acta DESC";
		//echo "f_actas: $ssql_1<br>";
		$oDBSt_sql_1=$oDB->query($ssql_1);
		$f_acta=$oDBSt_sql_1->fetchColumn();
		
		$ssql_2="UPDATE p_n_agd SET stgr='c1'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_2=$oDB->query($ssql_2);
		
		$ssql_3="INSERT INTO e_notas(id_nom,id_nivel,id_asignatura,f_acta,id_situacion,acta)
						 VALUES ($id_nom,'9999','9999','$f_acta',1,'fin bienio') ";
		$oDBSt_sql_3=$oDB->query($ssql_3);
	}
}
if ($_POST['actualizar']=="9998") {
	$ssql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*),stgr
		FROM p_n_agd p LEFT JOIN e_notas n USING (id_nom),e_notas_situacion s
		WHERE n.id_situacion=s.id_situacion AND s.superada='t'
			AND (n.id_nivel BETWEEN 2100 AND 2500 OR n.id_nivel=9998)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,stgr
		HAVING count(*) >= 52 AND Max(n.id_nivel)<>9998
		ORDER BY p.apellido1,p.apellido2,nom ";
	
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	$i=0;
	foreach ($oDBSt_sql->fetchAll() as $row) {
		$i++;
		$id_nom=$row["id_nom"];
		//busco su ultima fecha acta
		$ssql_1= "SELECT f_acta FROM e_notas WHERE id_nom=$id_nom and id_nivel between 2000 and 3000 
				ORDER BY f_acta DESC";
		$oDBSt_sql_1=$oDB->query($ssql_1);
		$f_acta=$oDBSt_sql_1->fetchColumn();
		
		$ssql_2="UPDATE p_n_agd SET stgr='r'
			WHERE id_nom=$id_nom
			";
		$oDBSt_sql_2=$oDB->query($ssql_2);
		
		$ssql_3="INSERT INTO e_notas(id_nom,id_nivel,id_asignatura,f_acta,id_situacion,acta)
						 VALUES ($id_nom,'9998','9998','$f_acta',1,'fin cuadrienio') ";
		$oDBSt_sql_3=$oDB->query($ssql_3);
	}
}
?>
<html>
<body topmargin="-0,5cm" background=/icons/fons.gif link=#0000ff vlink=#0000ff>
<h2><center><font color=red>Comprobación del Fichero de Notas</font></center></h2>
<hr size="2" aling="center">
</bODY>
</html>

<?php
//0. Asegurar que el año de fin de ce está puesto con 4 cifras

/*1. Gente con el bienio terminado y sin poner que lo ha terminado */
$sql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*) as num_asig,stgr
FROM p_n_agd p,e_notas n,e_notas_situacion s
WHERE p.id_nom=n.id_nom AND n.id_situacion=s.id_situacion AND s.superada='t'
	AND (n.id_nivel BETWEEN 1000 AND 2000 OR n.id_nivel=9999)
GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,stgr
HAVING count(*) >= 28 AND Max(n.id_nivel)<>9999
ORDER BY p.apellido1 ASC,p.apellido2 ";

$oDBSt_bienio=$oDB->query($sql);
$nf=$oDBSt_bienio->rowCount();
echo "<p>1. Gente con el bienio terminado y sin poner que lo ha terminado : $nf</p>";
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
	$pag="<a href=./comprobar_notas.php?actualizar=9999>"._("click aquí")."</a>";
	printf (_("Para poner c1 y bienio finalizado a todos los de la lista, hacer %s. Esto pondrá la fecha de acta última."),$pag);
}



/*2. Gente con el cuadrienio terminado y sin poner que lo ha terminado */
$sql="SELECT p.id_nom, p.nom, p.apellido1,p.apellido2,count(*) as num_asig,stgr
		FROM p_n_agd p LEFT JOIN e_notas n USING (id_nom),e_notas_situacion s
		WHERE n.id_situacion=s.id_situacion AND s.superada='t'
			AND (n.id_nivel BETWEEN 2100 AND 2500 OR n.id_nivel=9998)
		GROUP BY p.id_nom,p.nom, p.apellido1,p.apellido2,stgr
		HAVING count(*) >= 52 AND Max(n.id_nivel)<>9998
		ORDER BY p.apellido1,p.apellido2,nom ";
		
$oDBSt_cuadrienio=$oDB->query($sql);
$nf=$oDBSt_cuadrienio->rowCount();
echo "<p>2. Gente con el cuadrienio terminado y sin poner que lo ha terminado : $nf</p>";

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
	$pag="<a href=./comprobar_notas.php?actualizar=9998>"._("click aquí")."</a>";
	printf (_("Para poner r y cuadrienio finalizado a todos los de la lista, hacer %s. Esto pondrá la fecha de acta última."),$pag);
}

/*3. Gente con opcionales genéricas sin fecha o ref. a la opcional que es (no cuento las de la Ratio 89)*/

/*4. Gente sin fecha en acta (no cuento las de la Ratio 89)*/

$sqlF="SELECT  p.id_nom,p.nom, p.apellido1, p.apellido2, n.f_acta, a.nombre_corto
FROM p_n_agd p,e_notas n,xa_asignaturas a
WHERE p.id_nom=n.id_nom AND n.id_asignatura=a.id_Asignatura
	AND (n.f_acta) IS NULL
ORDER BY p.apellido1,p.apellido2 ";
$oDBSt_sql=$oDB->query($sqlF);
$nf=$oDBSt_sql->rowCount();
echo "<p>4. Gente con asignaturas sin fecha de acta: $nf</p>";

/* Para sacar una lista*/
echo "<table>";
foreach ($oDBSt_sql->fetchAll() as $algo) {
	$nom= $algo['apellido1']." ".$algo['apellido2'].", ".$algo['nom'];
	$fecha= $algo['f_acta'];
	$asig= $algo['nombre_corto'];
	echo "<tr><td width=20></td>";
	echo "<td>$nom</td><td>$fecha</td><td>$asig</td></tr>";
}
echo "<tr><td colspan=7><hr>";
echo "</table>";
/* end lista */

// 5. Comprobar que los de año I tienen puesto c1
$tabla="p_n_agd";
$ssql="SELECT p.stgr,p.nom, p.apellido1, p.apellido2, count(*) AS NumAsig
	FROM $tabla p LEFT JOIN e_notas n USING (id_nom)
	WHERE p.stgr != 'b' AND p.stgr != 'r' AND p.stgr !='c1'
		AND ((n.id_nivel BETWEEN 2100 AND 2113) OR n.id_nivel=2430)
	GROUP BY p.id_nom,p.stgr,p.nom, p.apellido1, p.apellido2
	HAVING count(*) < 14 
	ORDER BY apellido1,apellido2,nom"; 

$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
if (!empty($nf)) {
	echo "<p>5. Gente con \"c1\" mal puesto: $nf</p>";
	// Para sacar una lista
	$p=Lista($ssql,"stgr,nom,apellido1,apellido2","cabecera");
	$pag="<a href=./comprobar_notas.php?actualizar=c1>"._("click aquí")."</a>";
	printf (_("Para poner c1 a todos los de la lista, hacer %s"),$pag);
}

// 6. Comprobar que los de año II-IV tienen puesto c2
$tabla="p_n_agd";
$ssql="SELECT p.stgr,p.nom, p.apellido1, p.apellido2, count(*) AS NumAsig
	FROM $tabla p LEFT JOIN e_notas n USING (id_nom)
	WHERE p.stgr != 'b' AND p.stgr != 'r' AND p.stgr !='c2'
		AND ((n.id_nivel BETWEEN 2100 AND 2113) OR n.id_nivel=2430)
	GROUP BY p.id_nom,p.stgr,p.nom, p.apellido1, p.apellido2
	HAVING count(*) > 13 
	ORDER BY apellido1,apellido2,nom"; 
	
$oDBSt_sql=$oDB->query($ssql);
$nf=$oDBSt_sql->rowCount();
if (!empty($nf)) {
	echo "<p>6. Gente con \"c2\" mal puesto: $nf</p>";
	// Para sacar una lista
	$p=Lista($ssql,"stgr,nom,apellido1,apellido2","cabecera");
	$pag="<a href=./comprobar_notas.php?actualizar=c2>"._("click aquí")."</a>";
	printf (_("Para poner c2 a todos los de la lista, hacer %s"),$pag);
}


echo "</body>";
?>
