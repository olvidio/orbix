<?php
namespace documentos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula doc_colecciones
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
/**
 * Classe que implementa l'entitat doc_colecciones
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
class Coleccion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Coleccion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Coleccion
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded de Coleccion
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de Coleccion
	 *
	 * @var integer
	 */
	 private $iid_schema;

	/**
	 * Id_coleccion de Coleccion
	 *
	 * @var integer
	 */
	 private $iid_coleccion;
	/**
	 * Nom_coleccion de Coleccion
	 *
	 * @var string
	 */
	 private $snom_coleccion;
	/**
	 * Agrupar de Coleccion
	 *
	 * @var boolean
	 */
	 private $bagrupar;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Coleccion
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Coleccion
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_coleccion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_coleccion') && $val_id !== '') $this->iid_coleccion = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_coleccion = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_coleccion' => $this->iid_coleccion);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('doc_colecciones');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['nom_coleccion'] = $this->snom_coleccion;
		$aDades['agrupar'] = $this->bagrupar;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['agrupar']) ) { $aDades['agrupar']='true'; } else { $aDades['agrupar']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					nom_coleccion            = :nom_coleccion,
					agrupar                  = :agrupar";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_coleccion='$this->iid_coleccion'")) === FALSE) {
				$sClauError = 'Coleccion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Coleccion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(nom_coleccion,agrupar)";
			$valores="(:nom_coleccion,:agrupar)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Coleccion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Coleccion.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_coleccion = $oDbl->lastInsertId('doc_colecciones_id_coleccion_seq');
		}
		$this->setAllAtributes($aDades);
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_coleccion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_coleccion='$this->iid_coleccion'")) === FALSE) {
				$sClauError = 'Coleccion.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return FALSE;
					break;
                default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return TRUE;
		} else {
		   	return FALSE;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_coleccion='$this->iid_coleccion'")) === FALSE) {
			$sClauError = 'Coleccion.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_coleccion',$aDades)) $this->setId_coleccion($aDades['id_coleccion']);
		if (array_key_exists('nom_coleccion',$aDades)) $this->setNom_coleccion($aDades['nom_coleccion']);
		if (array_key_exists('agrupar',$aDades)) $this->setAgrupar($aDades['agrupar']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_coleccion('');
		$this->setNom_coleccion('');
		$this->setAgrupar('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Coleccion en un array
	 *
	 * @return array aDades
	 */
	function getTot() {
		if (!is_array($this->aDades)) {
			$this->DBCarregar('tot');
		}
		return $this->aDades;
	}

	/**
	 * Recupera las claus primàries de Coleccion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_coleccion' => $this->iid_coleccion);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de Coleccion en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_coleccion') && $val_id !== '') $this->iid_coleccion = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_coleccion = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_coleccion' => $this->iid_coleccion);
			}
		}
	}
	

	/**
	 * Recupera l'atribut iid_coleccion de Coleccion
	 *
	 * @return integer iid_coleccion
	 */
	function getId_coleccion() {
		if (!isset($this->iid_coleccion) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_coleccion;
	}
	/**
	 * estableix el valor de l'atribut iid_coleccion de Coleccion
	 *
	 * @param integer iid_coleccion
	 */
	function setId_coleccion($iid_coleccion) {
		$this->iid_coleccion = $iid_coleccion;
	}
	/**
	 * Recupera l'atribut snom_coleccion de Coleccion
	 *
	 * @return string snom_coleccion
	 */
	function getNom_coleccion() {
		if (!isset($this->snom_coleccion) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->snom_coleccion;
	}
	/**
	 * estableix el valor de l'atribut snom_coleccion de Coleccion
	 *
	 * @param string snom_coleccion='' optional
	 */
	function setNom_coleccion($snom_coleccion='') {
		$this->snom_coleccion = $snom_coleccion;
	}
	/**
	 * Recupera l'atribut bagrupar de Coleccion
	 *
	 * @return boolean bagrupar
	 */
	function getAgrupar() {
		if (!isset($this->bagrupar) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bagrupar;
	}
	/**
	 * estableix el valor de l'atribut bagrupar de Coleccion
	 *
	 * @param boolean bagrupar='f' optional
	 */
	function setAgrupar($bagrupar='f') {
		$this->bagrupar = $bagrupar;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oColeccionSet = new core\Set();

		$oColeccionSet->add($this->getDatosNom_coleccion());
		$oColeccionSet->add($this->getDatosAgrupar());
		return $oColeccionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snom_coleccion de Coleccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_coleccion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_coleccion'));
		$oDatosCampo->setEtiqueta(_("nombre de la colección"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('30');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bagrupar de Coleccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAgrupar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'agrupar'));
		$oDatosCampo->setEtiqueta(_("agrupar"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
}
