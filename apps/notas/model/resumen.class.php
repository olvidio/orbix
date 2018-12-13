<?php
namespace notas\model;
use core;
use actividades\model\entity as actividades;
use asignaturas\model\entity as asignaturas;
use personas\model\entity as personas;
use profesores\model\entity as profesores;

/**
 * Fitxer amb la Classe que accedeix a la taula e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
/**
 * Classe que implementa l'entitat e_notas_situacion
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
	protected $iany2;
	protected $diniverano;
	protected $sce_lugar;

	protected $a_asignaturas;
	protected $a_creditos;

	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Acta
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Acta
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	 protected $sNomNotas;
	 protected $sNomPersonas;
	 protected $sNomAsignaturas;


	 /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_nom,iid_nivel
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($nom='') {
		$oDbl = $GLOBALS['oDB'];

		$tabla="tmp_est_".$nom;
		$notas="tmp_notas_".$nom;
		$asignaturas="tmp_asignaturas";
		switch ($nom) {
			case 'numerarios':
				$personas="p_numerarios";
				break;
			case 'agd':
			case 'agregados':
				$personas="p_agregados";
				break;
			case 'profesores':
				$personas="personas_dl";
				break;
		}

		$this->setoDbl($oDbl);
		$this->setNomTabla($tabla);
		$this->setNomNotas($notas);
		$this->setNomAsignaturas($asignaturas);
		$this->setNomPersonas($personas);

	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	public function getNomPersonas() {
		return $this->sNomPersonas;
	}
	public function setNomPersonas($personas) {
			$this->sNomPersonas = $personas;
	}
	public function getNomNotas() {
		return $this->sNomNotas;
	}
	public function setNomNotas($notas) {
			$this->sNomNotas = $notas;
	}
	public function getNomAsignaturas() {
		return $this->sNomAsignaturas;
	}
	public function setNomAsignaturas($asignaturas) {
			$this->sNomAsignaturas = $asignaturas;
	}
	public function getLista() {
		return $this->blista;
	}
	public function setLista($blista) {
			$this->blista = $blista;
	}
	public function getCe_lugar() {
		return $this->sce_lugar;
	}
	public function setCe_lugar($sce_lugar) {
			$this->sce_lugar = $sce_lugar;
	}
	public function getAnyIniCurs() {
		if (empty($this->iany)) {
			$this->iany = date("Y");
		}
		return $this->iany;
	}
	public function setAnyIniCurs($iany) {
			$this->iany = $iany;
	}
	public function getAnyFiCurs() {
		if (empty($this->iany2)) {
			$this->iany2 = $this->getAnyIniCurs()+1 ;
		}
		return $this->iany2;
	}
	public function setAnyFiCurs($iany2) {
			$this->iany = $iany2;
	}

	public function getIniCurso() {
		if (empty($this->dinicurso)) {
			$any = $this->getAnyIniCurs();
			$this->dinicurso= date("Y-m-d", mktime(0,0,0,10,1,$any)) ;
		}
		return $this->dinicurso;
	}
	public function setIniCurso($dinicurso) {
			$this->dinicurso = $dinicurso;
	}
	public function getFinCurso() {
		if (empty($this->dfincurso)) {
			$any = $this->getAnyFiCurs();
			$this->dfincurso= date("Y-m-d", mktime(0,0,0,9,30,$any)) ;
		}
		return $this->dfincurso;
	}
	public function setFinCurso($dfincurso) {
			$this->dfincurso = $dfincurso;
	}

	/* Pongo en la variable $curso el periodo del curso */
	public function getCurso() {
		$curso = "BETWEEN '".$this->getIniCurso()."' AND '".$this->getFinCurso()."' ";
		return $curso;
	}
	
	/*
	$tabla="tmp_est_numerarios";
	$personas="p_numerarios";
	$notas="tmp_notas_numerarios";
	*/

	public function nuevaTabla() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		$asignaturas = $this->getNomAsignaturas();
		$personas = $this->getNomPersonas();

		$curs = $this->getCurso();
		$fincurs = $this->getFinCurso();

		$any = $this->getAnyIniCurs();

		$sqlDelete="DELETE FROM $tabla";
		$sqlCreate="CREATE TABLE $tabla(
										id_nom int4 NOT NULL PRIMARY KEY,
										id_tabla char(6),
										nom varchar(40),
										apellido1  varchar(25),
										apellido2  varchar(25),
										stgr char(2),
										situacion char(1),
										f_situacion date, 
										f_o date,
										f_fl date,
										f_orden date,
										ce_lugar varchar(40),
										ce_ini int2,
										ce_fin int2,
										sacd bool,
										ctr text )";
	
		if( !$oDbl->query($sqlDelete) ) {
				$oDbl->query($sqlCreate);
				$oDbl->query("CREATE INDEX $tabla"."_apellidos"." ON $tabla (apellido1,apellido2,nom)");
				$oDbl->query("CREATE INDEX $tabla"."_stgr"." ON $tabla (stgr)");
		}
		/*
		   * OOJO De momento estos campos no existen:
				f_o date,
				f_fl date,
				f_orden date,
				situacion char(1),

		$sqlLlenar="INSERT INTO $tabla 
				SELECT p.id_nom,p.id_tabla,p.nom,p.apellido1,p.apellido2,p.stgr,
				p.situacion,p.f_situacion,p.f_o,p.f_fl,p.f_orden,p.ce_lugar,p.ce_ini,p.ce_fin,p.situacion,p.sacd
				FROM $personas p
				WHERE ((p.situacion='A' AND (p.f_situacion < '$fincurs' OR p.f_situacion IS NULL)) OR (p.situacion='D' AND (p.f_situacion $curs)) OR (p.situacion='L' AND (p.f_orden $curs)))
				";
		   */
		
		$sqlLlenar="INSERT INTO $tabla 
				SELECT p.id_nom,p.id_tabla,p.nom,p.apellido1,p.apellido2,p.stgr,
				p.situacion,p.f_situacion,
				NULL,NULL,NULL,
				p.ce_lugar,p.ce_ini,p.ce_fin,
				p.sacd,u.nombre_ubi
				FROM $personas p LEFT JOIN u_centros_dl u ON (p.id_ctr = u.id_ubi)
				WHERE (p.situacion='A' AND (p.f_situacion < '$fincurs' OR p.f_situacion IS NULL))
					 OR (p.situacion='D' AND p.f_situacion $curs)
					 OR (p.situacion!='A' AND p.f_situacion > '$fincurs')
				";
		//echo "sql: $sqlLlenar<br>";
		$oDbl->query($sqlLlenar);

		// Busco los que han ido a un ci

		
	
		// Miro los que se han incorporado "recientemente": desde el 1-junio
		$ssql= "SELECT  p.nom, p.apellido1, p.apellido2, p.ctr, p.stgr
			FROM $tabla p
			WHERE p.situacion='A' AND p.f_situacion > '$any-6-1'
				AND (p.stgr='b' OR p.stgr ILIKE 'c%') "; 
		//echo "qry: $ssql<br>";
		$statement = $oDbl->query($ssql);
		$nf=$statement->rowCount();
		if ($this->blista && $nf!=0) {
			echo "<p>Existen $nf Alumnos que se han incorporado \"recientemente\" (desde el 1-junio) a la dl<br>
					Sí se cuentan en la estadística.</p>";
			// Para sacar una lista
			echo $this->Lista($ssql,"nom,apellido1,apellido2,ctr,stgr",1);
		}

		// Miro si existe alguna excepción: Alguien incorporado a la dl después del 1 de OCT 
		$ssql= "SELECT  p.nom, p.apellido1, p.apellido2, p.ctr, p.stgr
			FROM $tabla p
			WHERE (p.stgr='b' OR p.stgr ILIKE 'c%')
				AND (p.situacion='A' AND p.f_situacion > '$fincurs')"; 
		$statement=$oDbl->query($ssql);
		$nf=$statement->rowCount();
		
		if ($this->blista && $nf!=0) {
			echo "<p>Existen $nf alumnos que se han incorporado después del 1-OCT a la dl<br>
					No se van a contar</p>";
			// Para sacar una lista
			echo $this->Lista($ssql,"nom,apellido1,apellido2,ctr,stgr",1);
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
				$oDbl->query($sqlCreate);
				$oDbl->query("CREATE INDEX $notas"."_nivel"." ON $notas (id_nivel)");
				$oDbl->query("CREATE INDEX $notas"."_sup"." ON $notas (superada)");
		}

		$gesNotas = new entity\gestorNota();
		$a_superadas = $gesNotas->getArrayNotasSuperadas();
		$case_superada = " id_situacion IN (".implode(',', $a_superadas).")";
		$sqlLlenar="INSERT INTO $notas
					SELECT n.id_nom,n.id_asignatura,n.id_nivel,
						   $case_superada,
						   n.epoca,n.f_acta,n.acta,n.preceptor
					FROM $tabla p,e_notas_dl n
					WHERE p.id_nom=n.id_nom AND n.f_acta $curs
					";
		//echo "sql: $sqlLlenar<br>";
		$oDbl->query($sqlLlenar);

		//Ahora las asignaturas
		//Como ahora las asignaturas estan en otra base de datos(comun) hago una copia para poder hacer unions...
		$sqlDelete="DELETE FROM $asignaturas";
		$sqlCreate="CREATE TABLE $asignaturas(
						id_asignatura integer,
						id_nivel integer,
						nombre_asignatura character varying(100) NOT NULL,
						nombre_corto character varying(23),
						creditos numeric(4,2),
						year character varying(3),
						id_sector smallint,
						status boolean DEFAULT true NOT NULL,
						id_tipo integer
					 )";

		if (!$oDbl->query($sqlDelete) ) {
				$oDbl->query($sqlCreate);
				$oDbl->query("CREATE INDEX $asignaturas"."_nivel"." ON $asignaturas (id_nivel)");
				$oDbl->query("CREATE INDEX $asignaturas"."_id_asignatura"." ON $asignaturas (id_asignatura)");
		}

		$gesAsignaturas = new asignaturas\gestorAsignatura();
		$cAsignaturas = $gesAsignaturas->getAsignaturas(array('status'=>'true'));

		$prep = $oDbl->prepare("INSERT INTO $asignaturas VALUES(:id_asignatura, :id_nivel, :nombre_asignatura, :nombre_corto, :creditos, :year, :id_sector, :status, :id_tipo)");
		foreach ($cAsignaturas as $oAsignatura) {
			$aDades = $oAsignatura->getTot();
			$prep->execute($aDades);
		}
	}

	public function ListaAsig($a_Asql,$statement) {
		$oDbl = $this->getoDbl();
		// Para sacar una lista
		$html = "<table>";
		$id_nom=0;
		$cont=0; // para saber cuánta gente le queda
		$cont_asig=0;
		$cont_nom=0;
		$a_sql=$statement->fetchAll() ;
		foreach ($a_sql as $nombre) {
			$cont_nom++;
			// Si cambio de persona, vuelvo a empezar con las asignaturas
			if ($nombre["id_nom"]!=$id_nom){
				$cont_asig=0;
				$cont++;
				$id_nom= $nombre["id_nom"];
				$nom_ap=$nombre["nom_ap"];
				$html .= "<tr><td colspan=2 class=titulo>$nom_ap</td></tr>";
			}
			if ($cont_asig >= 28) {
				$html .= "Pasa de 28 asignaturas"; 
			} else {
				$asig_nivel=$a_Asql[$cont_asig]['id_nivel'];
				$cont_asig++;
				while ($nombre['id_nivel'] > $asig_nivel){
					
					$asig_nivel=$a_Asql[$cont_asig]['id_nivel'];
					$asig_nombre_corto=$a_Asql[$cont_asig]['nombre_corto'];
						
					$html .= "<tr><td></td><td>$asig_nombre_corto</td></tr>";
					$cont_asig++;
					if ($cont_asig > 28) exit ("Pasa de 28 asignaturas!!");
				}
			}
			//miro si el siguiente registro es de la misma persona, sino, pongo las asignaturas que quedan hasta acabar el bienio 
			if (count($a_sql) > $cont_nom) {
			$siguiente_id_nom=$a_sql[$cont_nom]['id_nom'];
				if ($siguiente_id_nom != $id_nom){
					while ($asig_nombre_corto=@$a_Asql[$cont_asig++]['nombre_corto']) {
						$html .= "<tr><td></td><td>$asig_nombre_corto</td></tr>";
					}
					//$cont_asig=0;
				}
			}
		}
		
		$html .= "<tr><td colspan=7><hr>";
		$html .= "</table>";
		// end lista 
		$html .= "<p>Total: $cont</p>";
		return $html;
	}


	public function Lista($sql,$campos,$cabecera) {
		$oDbl = $this->getoDbl();
		// $campos es un string con los campos que se quiere listar, separados por comas
		$camp=explode(',',$campos);
		$html = "<table>";
		if (!empty($cabecera)) {
			$html .= "<tr><td width=20></td>";
			foreach ($camp as $key => $titulo) {
				$html .= "<th>$titulo</th>";
			}
			$html .= "</tr>";
			$p=reset($camp);
		}
		foreach ($oDbl->query($sql) as $fila=>$valor) {
			$html .= "<tr><td width=20></td>";
			foreach ($camp as $key => $val) {
				$html .= "<td>$valor[$val]</td>";
			}
			$html .= "</tr>";
			$p=reset($camp);
		}
		if (empty($cabecera)) $html .= "<tr><td colspan=7><hr>";
		$html .= "</table>";

		return $html;
	}

	public function enBienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,ctr
		FROM $tabla p
		WHERE p.stgr='b' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	public function enCuadrienio($c='all') {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$where = '';
		switch ($c) {
			case 1:
				$where = "WHERE p.stgr='c1'";
				break;
			case 2:
				$where = "WHERE p.stgr='c2'";
				break;
			case 'all':
				$where = "WHERE p.stgr ~ '^c'";
				break;
		}
		$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM $tabla p
				$where 
				ORDER BY p.apellido1,p.apellido2,p.nom 
				"; 
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	public function enRepaso() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
		FROM $tabla p
		WHERE p.stgr='r' 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function enTotal() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$ssql="SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr,p.stgr
		FROM $tabla p
		WHERE p.stgr='b' OR p.stgr ILIKE 'c%'
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr,stgr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function enStgrSinO() {
		$iniverano = $this->diniverano;
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		/*
		$ssql="SELECT p.nom, p.apellido1, p.apellido2
		FROM $tabla p
		WHERE p.f_fl IS NULL
			AND (p.stgr='b' OR p.stgr ILIKE 'c%') AND (p.f_o > '$iniverano' OR p.f_o IS NULL)
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 

		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
		*/
		return array('num'=>'?','lista'=>'falta poner fecha o en tablas');
	}
	public function enCe() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$ce_lugar = $this->getCe_lugar();
		$any = $this->getAnyFiCurs();

	    $ssql="SELECT p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p
		WHERE (p.stgr='b')
			AND (p.ce_lugar='$ce_lugar' AND p.ce_ini IS NOT NULL  AND p.ce_fin IS NULL) 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 

		//echo "sql: $ssql<br>";
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	
	public function finCe() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$ce_lugar = $this->getCe_lugar();
		$any = $this->getAnyFiCurs();

	    $ssql="SELECT p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p
		WHERE (p.stgr='b' OR p.stgr ILIKE 'c%')
			AND (p.ce_lugar='$ce_lugar' AND p.ce_fin = '$any') 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 

		//echo "sql: $ssql<br>";
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function sinCe() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$ce_lugar = $this->getCe_lugar();
		$any = $this->getAnyFiCurs();

	    $ssql="SELECT p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p
		WHERE (p.stgr='b')
			AND (p.ce_lugar IS NULL OR p.ce_lugar = '') 
		ORDER BY p.apellido1,p.apellido2,p.nom 
		"; 

		//echo "sql: $ssql<br>";
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function aprobadasCe() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		$ce_lugar = $this->getCe_lugar();
		$any = $this->getAnyFiCurs();

	    $ssql="SELECT count(*)
			FROM $tabla p, $notas n
			WHERE p.id_nom=n.id_nom 
				AND (n.id_nivel BETWEEN 1100 AND 1229 OR n.id_nivel BETWEEN 2100 AND 2429)
				AND (p.ce_lugar='$ce_lugar' AND p.ce_fin = '$any')
			 	AND (p.stgr='b')
			"; 

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->fetchColumn();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = '';
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function aprobadasSinCe() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		$ce_lugar = $this->getCe_lugar();
		$any = $this->getAnyFiCurs();

	    $ssql="SELECT count(*)
			FROM $tabla p, $notas n
			WHERE p.id_nom=n.id_nom 
				AND (n.id_nivel BETWEEN 1100 AND 1229 OR n.id_nivel BETWEEN 2100 AND 2429)
				AND (p.ce_lugar ISNULL OR p.ce_lugar = '')
			 	AND (p.stgr='b')
			"; 

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->fetchColumn();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = '';
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	/**
	 * personas con stgr != 'b' y con FinBienio = NULL
	 * 
	 * @return array
	 */
	public function bienioSinAcabar() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();

		$rta = [];
		$ssql="SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				FROM $tabla p,$notas n
				WHERE p.id_nom=n.id_nom
					AND n.id_nivel=9999
					AND p.stgr != 'b'
				GROUP BY p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				ORDER BY p.apellido1, p.apellido2, p.nom
				";
		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}


	/**
	 * 
	 * @param integer $actual  0->todos, 1->este curso, 2->otros cursos
	 * @return array
	 */
	public function ceAcabadoEnBienio($actual=0) {
		$ce_lugar = $this->getCe_lugar();
		$any = $this->getAnyFiCurs();
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$rta = [];
		switch ($actual) {
			case 0: //todo
				$ssql="SELECT p.id_nom, p.apellido1, p.apellido2, p.nom, p.ctr, p.stgr
					FROM $tabla p
					WHERE  p.ce_fin IS NOT NULL AND p.ce_lugar = '$ce_lugar' AND p.stgr = 'b'
					ORDER BY p.apellido1,p.apellido2,p.nom  "; 
				$statement=$oDbl->query($ssql);
				$nf=$statement->rowCount();
				if ($nf >= 1){
					$rta['error'] = true;
					$rta['num'] = $nf;
					if ($this->blista == true && $rta['num'] > 0) {
						$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr,stgr",1);
					} else {
						$rta['lista'] = '';
					}
					return $rta;
				}
				break;
			case 1:
				$ssql="SELECT p.id_nom, p.apellido1, p.apellido2, p.nom, p.ctr, p.stgr
					FROM $tabla p
					WHERE  p.ce_fin='$any' AND p.ce_lugar = '$ce_lugar' AND p.stgr = 'b'
					ORDER BY p.apellido1,p.apellido2,p.nom  "; 
				$statement=$oDbl->query($ssql);
				$nf=$statement->rowCount();
				if ($nf >= 1){
					$rta['error'] = true;
					$rta['num'] = $nf;
					if ($this->blista == true && $rta['num'] > 0) {
						$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr,stgr",1);
					} else {
						$rta['lista'] = '';
					}
					return $rta;
				}
				break;
			case 2:
				$ssql="SELECT p.id_nom, p.apellido1, p.apellido2, p.nom, p.ctr, p.stgr
					FROM $tabla p
					WHERE  p.ce_fin != '$any' AND p.ce_lugar = '$ce_lugar' AND p.stgr = 'b'
					ORDER BY p.apellido1,p.apellido2,p.nom  "; 
				$statement=$oDbl->query($ssql);
				$nf=$statement->rowCount();
				if ($nf >= 1){
					$rta['error'] = true;
					$rta['num'] = $nf;
					if ($this->blista == true && $rta['num'] > 0) {
						$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr,stgr",1);
					} else {
						$rta['lista'] = '';
					}
					return $rta;
				}
				break;
		}
		return array('num'=>0,'lista'=>'');
	}



	public function aprobadasBienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		
		$ssql="SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				FROM $tabla p,$notas n
				WHERE p.id_nom=n.id_nom
					AND (n.id_nivel BETWEEN 1100 AND 1232)
					AND p.stgr ~ '^b'
				GROUP BY p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				ORDER BY p.apellido1, p.apellido2, p.nom
				";
		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = sprintf(_("total de asignaturas superadas en bienio %s"),$rta['num']);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function aprobadasCuadrienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();

		//Miro que no exista nadie de repaso que haya cursado alguna asignatura
		$ssql="SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				FROM $tabla p,$notas n
				WHERE p.id_nom=n.id_nom
					AND (n.id_nivel BETWEEN 2100 AND 2500)
					AND p.stgr='r'
				GROUP BY p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				ORDER BY p.apellido1, p.apellido2, p.nom
				";
		$statement=$oDbl->query($ssql);
		$nf=$statement->rowCount();
		if ($nf >= 1){
			$rta['error'] = true;
			$rta['num'] = $nf;
			if ($this->blista == true && $rta['num'] > 0) {
				$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
			} else {
				$rta['lista'] = '';
			}
			return $rta;
		}

//		$ssql="SELECT count(*)
//				FROM $notas n 
//				WHERE n.id_nivel BETWEEN 2100 AND 2500
//				 ";
//		$statement=$oDbl->query($ssql);
//		$rta['num'] = $statement->fetchColumn();
		$ssql="SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				FROM $tabla p,$notas n
				WHERE p.id_nom=n.id_nom
					AND (n.id_nivel BETWEEN 2100 AND 2500)
					AND p.stgr ~ '^c'
				GROUP BY p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
				ORDER BY p.apellido1, p.apellido2, p.nom
				";
		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = sprintf(_("total de asignaturas superadas en cuadrienio %s"),$rta['num']);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function masCreditosQue($creditos = '28.5') {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		$asignaturas = $this->getNomAsignaturas();

		$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p,$notas n,$asignaturas a
		WHERE p.id_nom=n.id_nom AND p.stgr ~ '^c' AND n.id_asignatura=a.id_asignatura
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) > $creditos
		ORDER BY p.apellido1,p.apellido2,p.nom  ";

		//echo "qry: $ssql<br>";
		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function menosCreditosQue($creditos = '14') {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		$asignaturas = $this->getNomAsignaturas();
		
		$ssql="SELECT n.id_nom,p.nom, p.apellido1,p.apellido2,p.ctr
		FROM $tabla p, $notas n, $asignaturas a
		WHERE p.id_nom=n.id_nom AND  n.id_nivel=a.id_nivel
			AND (p.stgr ILIKE 'c%' OR p.stgr='r')
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom,p.nom, p.apellido1,p.apellido2, p.ctr
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) <= $creditos
		ORDER BY p.apellido1,p.apellido2,p.nom  ";

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function ningunaSuperada() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		
		$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p LEFT JOIN $notas n USING (id_nom)
		WHERE p.stgr ~ '^c'
			AND n.id_nom IS NULL
		ORDER BY p.apellido1,p.apellido2,p.nom
		"; 

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function conPreceptorBienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		
		$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2,p.ctr
		FROM $notas n, $tabla p
		WHERE n.id_nom=p.id_nom AND n.preceptor='t' 
			AND p.stgr = 'b'
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		ORDER BY p.apellido1,p.apellido2,p.nom "; 

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function conPreceptorCuadrienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		
		$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2,p.ctr
		FROM $notas n, $tabla p
		WHERE n.id_nom=p.id_nom AND n.preceptor='t' 
			AND p.stgr ~ '^c'
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		ORDER BY p.apellido1,p.apellido2,p.nom "; 

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	public function terminadoCuadrienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		$curs = $this->getCurso();
		
		$ssql="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p, $notas n
		WHERE p.id_nom=n.id_nom
			AND (n.id_nivel=9998) AND n.f_acta $curs
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		ORDER BY p.apellido1, p.apellido2,p.nom"; 

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	public function laicosConCuadrienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		
		$ssql="SELECT p.id_nom,p.nom, p.apellido1, p.apellido2, p.ctr
			FROM $tabla p
			WHERE p.stgr='r' AND p.sacd='f'
			ORDER BY p.apellido1, p.apellido2,p.nom"; 

		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	// -------------------- Profesores ------------------------------------------

	/*
	 * Posibles Profesores
	 * posibles profesores (n y agd)
	 * posibles profesores asociados (añado s y sss+)
	 */	
	public function nuevaTablaProfe() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$personas = $this->getNomPersonas();
		
		// Finalmente no distingo porque los cojo todos de la tabla padre (personas_dl)
		$sqlDelete="DROP TABLE $tabla";
		$oDbl->query($sqlDelete);
		$sqlCreate="CREATE TABLE $tabla AS
						SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,u.nombre_ubi as ctr
		   				FROM $personas p JOIN d_profesor_stgr d USING(id_nom), u_centros_dl u
						WHERE situacion='A' AND (p.id_ctr = u.id_ubi)
						ORDER BY id_nom";
		$oDbl->query($sqlCreate);
		//echo "$sqlCreate<br>";
	
		/*
		try {
			$oDbl->query($sqlCreate);
			$oDbl->query("CREATE INDEX $tabla"."_id_nom"." ON $tabla (id_nom)");
		} catch (\PDOException $e) {
			echo $e->getMessage();
			$stmt = $oDbl->prepare($sqlDelete);
        	$stmt->execute();
			echo 'The number of row(s) deleted: ' . $deletedRows . '<br>';
		}
		 *
		 */
	}

	public function profesorDeTipo($id_tipo=0) {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$where_tipo = '';
		if ($id_tipo > 0) { $where_tipo = "id_tipo_profesor=$id_tipo AND"; }
		$ssql="SELECT DISTINCT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom)
				WHERE $where_tipo f_cese is null";
		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	public function profesorDeLatin() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$ssql="SELECT DISTINCT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_latin JOIN $tabla p USING (id_nom) 
				WHERE latin='t'";
		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	public function arrayProfesorDepartamento() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$ssql="SELECT DISTINCT p.id_nom,d.id_departamento 
				FROM $tabla p JOIN d_profesor_stgr d USING(id_nom)
				WHERE d.f_cese is null";
		$statement=$oDbl->prepare($ssql);
		$statement->execute();
		$result = $statement->fetchAll(\PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	/*44. Número de profesores que dieron clase de su especialidad*/
	public function profesorEspecialidad($otras=FALSE){
		$oDbl = $this->getoDbl();
		$any = $this->getAnyFiCurs();
		$curso_inicio = $any-1;
		$oGesSectores = new asignaturas\GestorSector();
		$a_sectores = $oGesSectores->getArraySectores();
		$asignaturas = $this->getNomAsignaturas();
		$a_profe_dept = $this->arrayProfesorDepartamento();
		$docencia_dep = array();
		$docencia_no_dep = array();
		$nombres = array();
		foreach ($a_profe_dept as $row) {
			$id_nom=$row['id_nom'];
			$id_departamento=$row['id_departamento'];
			// asignaturas (sector) por profesor. No contar las preceptuaciones
			$ssql="SELECT DISTINCT d.id_nom,d.id_activ,a.id_sector,a.nombre_corto"
					. " FROM d_docencia_stgr d JOIN $asignaturas a USING (id_asignatura)"
					. " WHERE d.id_nom=$id_nom AND curso_inicio=$curso_inicio AND d.tipo != 'p'";
			//echo "sql: $ssql<br>";
			foreach($oDbl->query($ssql) as $row) {
				$id_nom = $row['id_nom'];
				$id_activ = $row['id_activ'];
				$id_sector = $row['id_sector'];
				$nombre_corto = $row['nombre_corto'];
				if (in_array($id_sector, $a_sectores[$id_departamento])) {
					$docencia_dep[$id_nom] = 1;
				} else {
					$docencia_no_dep[$id_nom] = 1;
				}

				if ($this->blista == true ) {
					$oPersonaDl = new personas\PersonaDl($id_nom);
					$nom = $oPersonaDl->getNom();
					$apellido1 = $oPersonaDl->getApellido1();
					$apellido2 = $oPersonaDl->getApellido2();

					$nom_activ = '';
					if (!empty($id_activ)) {
						$oActividad = new actividades\Actividad($id_activ);
						$nom_activ = $oActividad->getNom_activ();
					}
					$nombres[$id_nom] = array('nom'=>$nom,
												'apellido1'=>$apellido1,
												'apellido2'=>$apellido2,
												'asignatura'=>$nombre_corto,
												'actividad'=>$nom_activ);
				}
			}
		}
		if ($otras) {
			$rta['num'] = count($docencia_no_dep);
			$a_docencia = $docencia_no_dep;
		} else {
			$rta['num'] = count($docencia_dep);
			$a_docencia = $docencia_dep;
		}

		if ($this->blista == true && $rta['num'] > 0) {
			//$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2",1);
			$camp=explode(',','nom,apellido1,apellido2,asignatura,actividad');
			$html = "<table>";
			$html .= "<tr><td width=20></td>";
			foreach ($camp as $key => $titulo) {
				$html .= "<th>$titulo</th>";
			}
			$html .= "</tr>";
			$p=reset($camp);
			foreach ($a_docencia as $id_nom=>$valor) {
				$html .= "<tr><td width=20></td>";
				foreach ($camp as $key => $val) {
					$data = $nombres[$id_nom][$val];
					$html .= "<td>$data</td>";
				}
				$html .= "</tr>";
				$p=reset($camp);
			}
			$html .= "<tr><td colspan=7><hr>";
			$html .= "</table>";

			$rta['lista'] = $html;
		} else {
			$rta['lista'] = '';
		}

		return $rta;
	}
	
	/*42. Número de profesores asistentes a congresos...*/
	public function ProfesorCongreso() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();
		$notas = $this->getNomNotas();
		$curs = $this->getCurso();
		
		$ssql="SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2, p.ctr
				FROM d_congresos JOIN $tabla p USING (id_nom) WHERE f_ini $curs ";
		//echo "$ssql<br>";
		$statement=$oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}

	// Profesores de Bienio.
	public function ProfesoresEnBienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$ssql="SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom) 
				WHERE f_cese is null AND id_departamento=1
				ORDER BY p.apellido1,p.apellido2,p.nom 
				"; 
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	// Profesores de Cuadrienio.
	public function ProfesoresEnCuadrienio() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$ssql="SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom) 
				WHERE f_cese is null AND id_departamento!=1
				ORDER BY p.apellido1,p.apellido2,p.nom 
				"; 
		$statement = $oDbl->query($ssql);
		$rta['num'] = $statement->rowCount();
		if ($this->blista == true && $rta['num'] > 0) {
			$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
	// Numero de departamentos con director
	public function Departamentos() {
		$oDbl = $this->getoDbl();
		$tabla = $this->getNomTabla();

		$oGesDirectores = new profesores\GestorProfesorDirector();
		$cDirectores = $oGesDirectores->getProfesoresDirectores(array('f_cese'=>1), array('f_cese' => 'IS NULL'));
		
		$rta['num'] = count($cDirectores);
		if ($this->blista == true && $rta['num'] > 0) {
			$html = '<table>';
			foreach ($cDirectores as $oDirector) {
				$id_departamento = $oDirector->getId_departamento();
				$id_nom = $oDirector->getId_nom();
				$oDepartamento = new asignaturas\Departamento($id_departamento);
				$nom_dep = $oDepartamento->getDepartamento();
				$oPersonaDl = new personas\PersonaDl($id_nom);
				$nom_persona = $oPersonaDl->getApellidosNombre();
				$html .= "<tr><td>$nom_dep</td><td>$nom_persona</td></tr>";
			}
			$html .= '</table>';
			$rta['lista'] = $html;
		} else {
			$rta['lista'] = '';
		}
		return $rta;
	}
}
