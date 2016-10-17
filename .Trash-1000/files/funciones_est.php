<?php
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************



function BorrarTablas($tabla,$personas,$notas,$lista=0) {
	$oDB = $GLOBALS['oDB'];

	/* Pongo en la variable $curs el periodo del curso */
	$any=date("Y");
	
	$EstiuCurs=date("d/m/Y", mktime(0,0,0,7,2,$any)) ;
	$inicurs= date("d/m/Y", mktime(0,0,0,10,1,$any-1)) ;
	$fincurs= date("d/m/Y", mktime(0,0,0,9,30,$any)) ;
	$curs="BETWEEN '$inicurs' AND '$fincurs' ";

	$sqlDelete="DELETE FROM $tabla";
	$sqlCreate="CREATE TABLE $tabla(
										id_nom int4 NOT NULL PRIMARY KEY,
										id_tabla char(6),
										nom varchar(20),
										apellido1  varchar(25),
										apellido2  varchar(25),
										stgr char(2),
										fichero char(1),
										f_fichero date, 
										f_o date,
										f_fl date,
										f_orden date,
										lugar_ce varchar(8),
										ini_ce int2,
										fin_ce int2,
										vida_familia char(1),
										sacd bool )";
	
	if( !$oDB->query($sqlDelete) ) {
			$oDBSt_tab=$oDB->query($sqlCreate);
			$oDBSt_tab=$oDB->query("CREATE INDEX $tabla_apellidos ON $tabla (apellido1,apellido2,nom)");
			$oDBSt_tab=$oDB->query("CREATE INDEX $tabla_stgr ON $tabla (stgr)");
	}
	
		
	$sqlLlenar="INSERT INTO $tabla 
				SELECT p.id_nom,p.id_tabla,p.nom,p.apellido1,p.apellido2,p.stgr,
				p.fichero,p.f_fichero,p.f_o,p.f_fl,p.f_orden,p.lugar_ce,p.ini_ce,p.fin_ce,p.vida_familia,p.sacd
				FROM $personas p
				WHERE ((p.fichero='A' AND (p.f_fichero < '$fincurs' OR p.f_fichero IS NULL)) OR (p.fichero='D' AND (p.f_fichero $curs)) OR (p.fichero='L' AND (p.f_orden $curs)))
				";
	$oDBSt_Numeraris=$oDB->query($sqlLlenar);
	
	// Miro los que se han incorporado "recientemente": desde el 1-junio
	$ssql= "SELECT  p.nom, p.apellido1, p.apellido2, p.stgr
		FROM $tabla p
		WHERE p.fichero='A' AND p.f_fichero > '1/6/$any'
			AND (p.stgr='b' OR p.stgr ILIKE 'c%') "; 
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	if ($lista && $nf!=0) {
		echo "<p>Existen $nf Alumnos que se han incorporado \"recientemente\" (desde el 1-junio) a la dl<br>
				Sí se cuentan en la estadística.</p>";
		// Para sacar una lista
		$p=Lista($ssql,"nom,apellido1,apellido2,stgr",1);
	}
	// Miro si existe alguna excepción: Alguien incorporado a la dl después del 1 de OCT 
	$ssql= "SELECT  p.nom, p.apellido1, p.apellido2, p.stgr
		FROM $tabla p
		WHERE (p.stgr='b' OR p.stgr ILIKE 'c%')
			AND (p.fichero='A' AND p.f_fichero > '$fincurs')"; 
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	if ($lista && $nf!=0) {
		echo "<p>Existen $nf alumnos que se han incorporado después del 1-OCT a la dl<br>
				No se van a contar</p>";
		// Para sacar una lista
		$p=Lista($ssql,"nom,apellido1,apellido2,stgr",1);
	}
	
	//Pongo 'b' en stgr a los que han terminado el bienio este curso
	$ssql="UPDATE $tabla SET stgr='b'
			FROM e_notas n
			WHERE $tabla.id_nom=n.id_nom AND n.id_asignatura=9999 AND n.f_acta $curs
			 "; 
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	//Pongo 'c2' en stgr a los que han terminado el cuadrienio este curso
	$ssql="UPDATE $tabla SET stgr='c2'
			FROM e_notas n
			WHERE $tabla.id_nom=n.id_nom AND n.id_asignatura=9998 AND n.f_acta $curs
			 "; 
	$oDBSt_sql=$oDB->query($ssql);
	$nf=$oDBSt_sql->rowCount();
	
	//Ahora las notas
	$sqlDelete="DELETE FROM $notas";
	$sqlCreate="CREATE TABLE $notas(
										id_nom int4 NOT NULL,
										id_asignatura int4 NOT NULL, 
										id_nivel int4 NOT NULL,
										superada bool,
										epoca int2,
										f_acta  date NOT NULL,
										acta  varchar(50),
										preceptor bool,
										PRIMARY KEY (id_nom,id_asignatura)
										 )";

	if (!$oDB->query($sqlDelete) ) {
			$oDBSt_tab=$oDB->query($sqlCreate);
			$oDBSt_tab=$oDB->query("CREATE INDEX $notas_nivel ON $notas (id_nivel)");
			$oDBSt_tab=$oDB->query("CREATE INDEX $notas_sup ON $notas (superada)");
	}

	$sqlLlenar="INSERT INTO $notas
				SELECT n.id_nom,n.id_asignatura,n.id_nivel,s.superada,n.epoca,n.f_acta,n.acta,n.preceptor
				FROM $tabla p,e_notas n,e_notas_situacion s
				WHERE p.id_nom=n.id_nom  AND n.f_acta $curs AND n.id_situacion=s.id_situacion
				";
	$oDBSt_notas=$oDB->query($sqlLlenar);
}	

function ListaAsig($a_Asql,$oDBSt_sql) {
	// Para sacar una lista
	echo "<table>";
	$id_nom=0;
	$cont=0; // para saber cuánta gente le queda
	$cont_asig=0;
	$cont_nom=0;
	$a_sql=$oDBSt_sql->fetchAll() ;
	foreach ($a_sql as $nombre) {
		$cont_nom++;
		// Si cambio de persona, vuelvo a empezar con las asignaturas
		if ($nombre["id_nom"]!=$id_nom){
			$cont_asig=0;
			$cont++;
			$id_nom= $nombre["id_nom"];
			$nom_ap=$nombre["nom_ap"];
			echo "<tr><td colspan=2 class=titulo>$nom_ap</td></tr>";
		}
		if ($cont_asig >= 28) {
			echo "Pasa de 28 asignaturas"; 
		} else {
			$asig_nivel=$a_Asql[$cont_asig]['id_nivel'];
			$cont_asig++;
			while ($nombre['id_nivel'] > $asig_nivel){
				
				$asig_nivel=$a_Asql[$cont_asig]['id_nivel'];
				$asig_nombre_corto=$a_Asql[$cont_asig]['nombre_corto'];
					
				echo "<tr><td></td><td>$asig_nombre_corto</td></tr>";
				$cont_asig++;
				if ($cont_asig > 28) exit ("Pasa de 28 asignaturas!!");
			}
		}
		//miro si el siguiente registro es de la misma persona, sino, pongo las asignaturas que quedan hasta acabar el bienio 
		if (count($a_sql) > $cont_nom) {
		$siguiente_id_nom=$a_sql[$cont_nom]['id_nom'];
			if ($siguiente_id_nom != $id_nom){
				while ($asig_nombre_corto=@$a_Asql[$cont_asig++]['nombre_corto']) {
					echo "<tr><td></td><td>$asig_nombre_corto</td></tr>";
				}
				//$cont_asig=0;
			}
		}
	}
	
	echo "<tr><td colspan=7><hr>";
	echo "</table>";
	// end lista 
	echo "<p>Total: $cont</p>";
}


function Lista($sql,$campos,$cabecera) {
	$oDB = $GLOBALS['oDB'];
	// $campos es un string con los campos que se quiere listar, separados por comas
	$camp=explode(',',$campos);
	echo "<table>";
	if (!empty($cabecera)) {
		echo "<tr><td width=20></td>";
		while ( list( $key, $titulo ) = each( $camp ) ) {
			echo "<th>$titulo</th>";
		}
		echo "</tr>";
		$p=reset($camp);
	}
	foreach ($oDB->query($sql) as $fila=>$valor) {
		echo "<tr><td width=20></td>";
		while ( list( $key, $val ) = each( $camp ) ) {
			echo "<td>$valor[$val]</td>";
		}
		echo "</tr>";
		$p=reset($camp);
	}
	if (empty($cabecera)) echo "<tr><td colspan=7><hr>";
	echo "</table>";
	// end lista 
}
?>
