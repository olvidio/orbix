<?php
namespace notas\model;
use core;
use asignaturas\model as asignaturas;

/**
 * Classe que implementa l'entitat e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class AsignaturasPendientes Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * oDbl de Acta
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * 
	 *
	 * @var string
	 */
	 protected $sNomPersonas;
	 protected $sNomAsignaturas;

	/**
	 * Lista. para indicar si devuelve la lista de nombres o sólo el número
	 *
	 * @var boolean
	 */
	 protected $blista;

	protected $iasignaturasB;
	protected $iasignaturasC;
	protected $iasignaturasC1;
	protected $iasignaturasC2;
	protected $aIdNivel;



	 /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_nom,iid_nivel
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($personas='') {
		$oDbl = $GLOBALS['oDB'];

		$this->setoDbl($oDbl);
		if (!empty($personas)) {
			$this->setNomPersonas($personas); 
		}
		$this->setNomAsignaturas('tmp_xa_asignaturas'); 
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	public function getNomPersonas() {
		return $this->sNomPersonas;
	}
	public function setNomPersonas($personas) {
			$this->sNomPersonas = $personas;
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
	public function getAsignaturasB() {
		if (empty($this->iasignaturasB)) {
			$gesAsignaturas = new asignaturas\gestorAsignatura();
			$cAsignaturasB = $gesAsignaturas->getAsignaturas(array('status'=>'t','id_nivel'=>'1100,1300'),array('id_nivel'=>'BETWEEN'));

			$this->iasignaturasB = count($cAsignaturasB);
			$aIdNivel = array();
			foreach ($cAsignaturasB as $oAsignatura) {
				$aIdNivel[] = $oAsignatura->getId_nivel();
			}
			$this->aIdNivel = $aIdNivel;
		}
		return $this->iasignaturasB;
	}
	public function setAsignaturasB($asignaturasB) {
		$this->iasignaturasB =  $asignaturasB;
	}
	public function getAsignaturasC() {
		if (empty($this->iasignaturasC)) {
			$gesAsignaturas = new asignaturas\gestorAsignatura();
			$cAsignaturasC = $gesAsignaturas->getAsignaturas(array('status'=>'t','id_nivel'=>'2100,2500'),array('id_nivel'=>'BETWEEN'));

			$this->iasignaturasC = count($cAsignaturasC);
			$aIdNivel = array();
			foreach ($cAsignaturasC as $oAsignatura) {
				$aIdNivel[] = $oAsignatura->getId_nivel();
			}
			$this->aIdNivel = $aIdNivel;
		}
		return $this->iasignaturasC;
	}
	public function setAsignaturasC($asignaturasC) {
		$this->iasignaturasC =  $asignaturasC;
	}
	public function getAsignaturasC1() {
		if (empty($this->iasignaturasC1)) {
			$gesAsignaturas = new asignaturas\gestorAsignatura();
			$cAsignaturasC1 = $gesAsignaturas->getAsignaturas(array('status'=>'t','id_nivel'=>'2100,2113'),array('id_nivel'=>'BETWEEN'));
			// le sumo una opcional (id_nivel = 2430)
			$this->iasignaturasC1 = count($cAsignaturasC1)+1;
			$aIdNivel = array();
			foreach ($cAsignaturasC1 as $oAsignatura) {
				$aIdNivel[] = $oAsignatura->getId_nivel();
			}
			$aIdNivel[] = 2430;
			$this->aIdNivel = $aIdNivel;
		}
		return $this->iasignaturasC1;
	}
	public function setAsignaturasC1($asignaturasC1) {
		$this->iasignaturasC1 =  $asignaturasC1;
	}
	public function getAsignaturasC2() {
		if (empty($this->iasignaturasC2)) {
			$gesAsignaturas = new asignaturas\gestorAsignatura();
			$cAsignaturasC2 = $gesAsignaturas->getAsignaturas(array('status'=>'t','id_nivel'=>'2200,2500'),array('id_nivel'=>'BETWEEN'));
			// le quito una opcional (id_nivel = 2430)
			$this->iasignaturasC2 = count($cAsignaturasC2)-1;
			$aIdNivel = array();
			foreach ($cAsignaturasC2 as $oAsignatura) {
				if (($id_nivel = $oAsignatura->getId_nivel()) == 2430) continue;
				$aIdNivel[] = $oAsignatura->getId_nivel();
			}
			$this->aIdNivel = $aIdNivel;
		}
		return $this->iasignaturasC2;
	}
	public function setAsignaturasC2($asignaturasC2) {
		$this->iasignaturasC2 =  $asignaturasC2;
	}
	
	public function condicion($curso) {
		$num_curso = 0;
		switch ($curso) {
			case 'bienio':
				$num_curso = $this->getAsignaturasB();
				//$condicion="AND (n.id_nivel BETWEEN 1100 AND 1300) AND p.stgr='b'";
				$condicion = "AND id_nivel IN (".implode(',', $this->aIdNivel).")";
		 		$condicion_stgr = "AND p.stgr = 'b'";
				break;
			case 'cuadrienio':
				$num_curso = $this->getAsignaturasC();
				//$condicion="AND (n.id_nivel BETWEEN 2100 AND 2500) AND p.stgr ~ '^c'";
				$condicion="AND id_nivel IN (".implode(',', $this->aIdNivel).")";
		 		$condicion_stgr = "AND p.stgr ~ '^c'";
				break;
			case 'c1':
				$num_curso = $this->getAsignaturasC1();
				//$condicion="AND (n.id_nivel BETWEEN 3100 AND 2500) AND n.id_nivel!=2430 AND p.stgr ~ '^c'";
				$condicion="AND id_nivel IN (".implode(',', $this->aIdNivel).")";
		 		$condicion_stgr = "AND p.stgr ~ '^c'";
				break;
			case 'c2':
				$num_curso = $this->getAsignaturasC2();
				//$condicion="AND ((n.id_nivel BETWEEN 2100 AND 2113) OR n.id_nivel=2430) AND p.stgr ~ '^c'";
				$condicion="AND id_nivel IN (".implode(',', $this->aIdNivel).")";
		 		$condicion_stgr = "AND p.stgr ~ '^c'";
				break;
		}
		return array('num'=>$num_curso,'condicion'=>$condicion,'condicion_stgr'=>$condicion_stgr);
	}
	public function personasQueLesFalta($num_asignaturas,$curso) {
		$lista = $this->blista;
		$oDbl = $this->getoDbl();
		$personas = $this->getNomPersonas();

		$aCondicion = $this->condicion($curso);
		$num_curso = $aCondicion['num'];
		$condicion = $aCondicion['condicion'];
		$condicion_stgr = $aCondicion['condicion_stgr'];
		//echo "num = $num_curso<br>";
		$num = $num_curso - $num_asignaturas;
	
		$ssql = "SELECT p.id_nom, Count(*) as asignaturas
			FROM $personas p JOIN e_notas_dl n USING (id_nom)
			WHERE p.situacion='A'
			 $condicion $condicion_stgr
			GROUP BY  p.id_nom 
			HAVING Count(*) >= $num AND Count(*) < $num_curso
			ORDER BY p.apellido1 ";

		$aId_nom = array();
		foreach ( $oDbl->query($ssql) as $row) {
			$id_nom = $row['id_nom'];
			if ($lista == false) { // El numero de asignaturas que faltan
				$aId_nom[$id_nom] = $num_curso - $row['asignaturas'];
			} else { // El listado de asignaturas que faltan
				$aAsignaturas = $this->asignaturasQueFaltanPersona($id_nom,$curso);
				$aId_nom[$id_nom] = $aAsignaturas;
			}
		}
		return $aId_nom;
	}

	public function asignaturasQueFaltanPersona($id_nom,$curso) {
		$oDbl = $this->getoDbl();
		$asignaturas = $this->getNomAsignaturas();
		$this->createAsignaturas(); // crear tabla temporal asignaturas

		$aCondicion = $this->condicion($curso);
		$num_curso = $aCondicion['num'];
		$condicion = $aCondicion['condicion'];

		$query="SELECT a.nombre_corto, Notas.id_asignatura
				FROM $asignaturas a LEFT JOIN (SELECT id_asignatura from e_notas_dl where id_nom=$id_nom and id_asignatura < 3000) AS Notas USING (id_asignatura)
				WHERE a.id_sector != 0 AND Notas.id_asignatura is null
				$condicion
				";
		$query_op="SELECT a.nombre_corto, Notas.id_nivel
				FROM $asignaturas a LEFT JOIN (SELECT id_nivel from e_notas_dl where id_nom=$id_nom and id_asignatura > 3000) AS Notas USING (id_nivel)
				WHERE a.id_sector = 0 AND Notas.id_nivel is null
				$condicion
				";
		$query_tot="$query UNION $query_op  ORDER BY 2";
		//echo "query asig: $query_tot<br>";

		$a_nomAsignaturas = array();
		foreach ($oDbl->query($query_tot) as $asig) {
			$a_nomAsignaturas[] = $asig["nombre_corto"];
		}
		return $a_nomAsignaturas;
	}

	public function createAsignaturas() {
		//Como ahora las asignaturas estan en otra base de datos(comun) hago una copia para poder hacer unions...
		//$sqlDelete="DELETE FROM $asignaturas";
		$oDbl = $this->getoDbl();
		$asignaturas = $this->getNomAsignaturas();

		$sqlDelete="DELETE FROM $asignaturas";
		$sqlCreate="CREATE TEMP TABLE $asignaturas(
						id_asignatura integer,
						id_nivel integer,
						nombre_asig character varying(60) NOT NULL,
						nombre_corto character varying(23),
						creditos numeric(4,2),
						year character varying(3),
						id_sector smallint,
						status boolean DEFAULT true NOT NULL,
						id_tipo integer
					 )";

			
		if( !$oDbl->query($sqlDelete) ) {
			$oDbl->query($sqlCreate);
			$oDbl->query("CREATE INDEX $asignaturas"."_nivel"." ON $asignaturas (id_nivel)");
			$oDbl->query("CREATE INDEX $asignaturas"."_id_asignatura"." ON $asignaturas (id_asignatura)");
		}

		$gesAsignaturas = new asignaturas\gestorAsignatura();
		$cAsignaturas = $gesAsignaturas->getAsignaturas(array('status'=>'true'));

		$prep = $oDbl->prepare("INSERT INTO $asignaturas VALUES(:id_asignatura, :id_nivel, :nombre_asig, :nombre_corto, :creditos, :year, :id_sector, :status, :id_tipo)");
		foreach ($cAsignaturas as $oAsignatura) {
			$aDades = $oAsignatura->getTot();
			$prep->execute($aDades);
		}
	}
}
