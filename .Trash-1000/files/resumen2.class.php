<?php
namespace notas\model;
use core;

/**
 * Classe que implementa 
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class Resumen Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * Lista. para indicar si devuelve la lista de nombres o sólo el número
	 *
	 * @var boolean
	 */
	 protected $blista;

	protected $dinicurso;
	protected $dfincurso;
	protected $iany;

	 /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_nom,iid_nivel
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		/*
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nivel') && $val_id !== '') $this->iid_nivel = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		*/
		$this->setoDbl($oDbl);
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	public function getLista() {
		return $this->blista;
	}
	public function setLista($blista) {
			$this->blista = $blista;
	}
	public function getAny() {
		if (empty($this->iany)) {
			$this->iany = date("Y");
		}
		return $this->iany;
	}
	public function setAny($iany) {
			$this->iany = $iany;
	}

	public function getIniCurso() {
		if (empty($this->dinicurso)) {
			$any = $this->getAny();
			$this->dinicurso= date("d/m/Y", mktime(0,0,0,10,1,$any-1)) ;
		}
		return $this->dinicurso;
	}
	public function setIniCurso($dinicurso) {
			$this->dinicurso = $dinicurso;
	}
	public function getFinCurso() {
		if (empty($this->dfincurso)) {
			$any = $this->getAny();
			$this->dfincurso= date("d/m/Y", mktime(0,0,0,9,30,$any)) ;
		}
		return $this->dfincurso;
	}
	public function setFinCurso($dfincurso) {
			$this->dfincurso = $dfincurso;
	}

	/* Pongo en la variable $curso el periodo del curso */
	public function getCurso() {
		$curso = "BETWEEN '".$this->getInicurso()."' AND '".$this->getFincurso()."' ";
		return $curso;
	}
	
	/*
	$tabla="tmp_est_numerarios";
	$personas="p_numerarios";
	$notas="tmp_notas_numerarios";
	*/

	public function nuevaTabla($nom='numerarios') {
		$lista = $this->blista;
		$oDbl = $this->getoDbl();

		$tabla="tmp_est_".$nom;
		$tabla_apellidos = $tabla."_apellidos";
		$tabla_stgr = $tabla."_stgr";
		$notas="tmp_notas_".$nom;
		switch ($nom) {
			case 'numerarios':
				$personas="p_numerarios";
				break;
			case 'agd':
				$personas="p_agregados";
				break;
		}
	
		$curs = $this->getCurso();
		$fincurs = $this->getFincurso();

		$any = $this->getAny();

		$sqlDelete="DELETE FROM $tabla";
		$sqlCreate="CREATE TABLE $tabla(
										id_nom int4 NOT NULL PRIMARY KEY,
										id_tabla char(6),
										nom varchar(20),
										apellido1  varchar(25),
										apellido2  varchar(25),
										stgr char(2),
										situacion char(1),
										f_situacion date, 
										f_o date,
										f_fl date,
										f_orden date,
										lugar_ce varchar(8),
										ini_ce int2,
										fin_ce int2,
										vida_familia char(1),
										sacd bool )";
	
		if( !$oDbl->query($sqlDelete) ) {
				$oDbl->query($sqlCreate);
				$oDbl->query("CREATE INDEX $tabla_apellidos ON $tabla (apellido1,apellido2,nom)");
				$oDbl->query("CREATE INDEX $tabla_stgr ON $tabla (stgr)");
		}
		/*
		   * OOJO De momento estos campos no existen:
				f_o date,
				f_fl date,
				f_orden date,
				lugar_ce varchar(8),
				ini_ce int2,
				fin_ce int2,
				vida_familia char(1),

		$sqlLlenar="INSERT INTO $tabla 
				SELECT p.id_nom,p.id_tabla,p.nom,p.apellido1,p.apellido2,p.stgr,
				p.situacion,p.f_situacion,p.f_o,p.f_fl,p.f_orden,p.lugar_ce,p.ini_ce,p.fin_ce,p.vida_familia,p.sacd
				FROM $personas p
				WHERE ((p.situacion='A' AND (p.f_situacion < '$fincurs' OR p.f_situacion IS NULL)) OR (p.situacion='D' AND (p.f_situacion $curs)) OR (p.situacion='L' AND (p.f_orden $curs)))
				";
		   */
		
		$sqlLlenar="INSERT INTO $tabla 
				SELECT p.id_nom,p.id_tabla,p.nom,p.apellido1,p.apellido2,p.stgr,
				p.situacion,p.f_situacion,
				\N,\N,\N,\N,\N,\N,\N,
				p.sacd
				FROM $personas p
				WHERE ((p.situacion='A' AND (p.f_situacion < '$fincurs' OR p.f_situacion IS NULL)) OR (p.situacion='D' AND (p.f_situacion $curs)) OR (p.situacion='L' AND (p.f_orden $curs)))
				";
		$oDbl->query($sqlLlenar);
	
		// Miro los que se han incorporado "recientemente": desde el 1-junio
		$ssql= "SELECT  p.nom, p.apellido1, p.apellido2, p.stgr
			FROM $tabla p
			WHERE p.situacion='A' AND p.f_situacion > '1/6/$any'
				AND (p.stgr='b' OR p.stgr ILIKE 'c%') "; 
		$statement = $oDbl->query($ssql);
		$nf=$statement->rowCount();
		if ($lista && $nf!=0) {
			echo "<p>Existen $nf Alumnos que se han incorporado \"recientemente\" (desde el 1-junio) a la dl<br>
					Sí se cuentan en la estadística.</p>";
			// Para sacar una lista
			echo $this->Lista($ssql,"nom,apellido1,apellido2,stgr",1);
		}

		// Miro si existe alguna excepción: Alguien incorporado a la dl después del 1 de OCT 
		$ssql= "SELECT  p.nom, p.apellido1, p.apellido2, p.stgr
			FROM $tabla p
			WHERE (p.stgr='b' OR p.stgr ILIKE 'c%')
				AND (p.situacion='A' AND p.f_situacion > '$fincurs')"; 
		$statement=$oDbl->query($ssql);
		$nf=$statement->rowCount();
		
		if ($lista && $nf!=0) {
			echo "<p>Existen $nf alumnos que se han incorporado después del 1-OCT a la dl<br>
					No se van a contar</p>";
			// Para sacar una lista
			echo $this->Lista($ssql,"nom,apellido1,apellido2,stgr",1);
		}
	
		//Pongo 'b' en stgr a los que han terminado el bienio este curso
		$ssql="UPDATE $tabla SET stgr='b'
				FROM e_notas_dl n
				WHERE $tabla.id_nom=n.id_nom AND n.id_asignatura=9999 AND n.f_acta $curs
				 "; 
		$statement=$oDbl->query($ssql);
		$nf=$statement->rowCount();
		
		//Pongo 'c2' en stgr a los que han terminado el cuadrienio este curso
		$ssql="UPDATE $tabla SET stgr='c2'
				FROM e_notas_dl n
				WHERE $tabla.id_nom=n.id_nom AND n.id_asignatura=9998 AND n.f_acta $curs
				 "; 
		$statement=$oDbl->query($ssql);
		$nf=$statement->rowCount();
		
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

		if (!$oDbl->query($sqlDelete) ) {
				$oDblSt_tab=$oDbl->query($sqlCreate);
				$oDblSt_tab=$oDbl->query("CREATE INDEX $notas_nivel ON $notas (id_nivel)");
				$oDblSt_tab=$oDbl->query("CREATE INDEX $notas_sup ON $notas (superada)");
		}

		$sqlLlenar="INSERT INTO $notas
					SELECT n.id_nom,n.id_asignatura,n.id_nivel,s.superada,n.epoca,n.f_acta,n.acta,n.preceptor
					FROM $tabla p,e_notas_dl n,e_notas_situacion s
					WHERE p.id_nom=n.id_nom  AND n.f_acta $curs AND n.id_situacion=s.id_situacion
					";
		$oDbl->query($sqlLlenar);
	}

	public function ListaAsig($a_Asql,$oDblSt_sql) {
		$oDbl = $this->getoDbl();
		// Para sacar una lista
		echo "<table>";
		$id_nom=0;
		$cont=0; // para saber cuánta gente le queda
		$cont_asig=0;
		$cont_nom=0;
		$a_sql=$oDblSt_sql->fetchAll() ;
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


	public function Lista($sql,$campos,$cabecera) {
		$oDbl = $this->getoDbl();
		// $campos es un string con los campos que se quiere listar, separados por comas
		$camp=explode(',',$campos);
		$html = "<table>";
		if (!empty($cabecera)) {
			$html .= "<tr><td width=20></td>";
			while ( list( $key, $titulo ) = each( $camp ) ) {
				$html .= "<th>$titulo</th>";
			}
			$html .= "</tr>";
			$p=reset($camp);
		}
		foreach ($oDbl->query($sql) as $fila=>$valor) {
			$html .= "<tr><td width=20></td>";
			while ( list( $key, $val ) = each( $camp ) ) {
				$html .= "<td>$valor[$val]</td>";
			}
			$html .= "</tr>";
			$p=reset($camp);
		}
		if (empty($cabecera)) $html .= "<tr><td colspan=7><hr>";
		$html .= "</table>";
		// end lista 
	}

	public function enBienio() {
		$oDbl = $this->getoDbl();
		$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2
		FROM $tabla p
		WHERE p.stgr='b' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

}
?>
