<?php
namespace devel\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula db_idschema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/06/2018
 */
/**
 * Classe que implementa l'entitat db_idschema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/06/2018
 */
class DbSchema Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de DbSchema
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de DbSchema
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Schema de DbSchema
	 *
	 * @var string
	 */
	 private $sschema;
	/**
	 * Id de DbSchema
	 *
	 * @var integer
	 */
	 private $iid;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de DbSchema
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de DbSchema
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
	 * @param integer|array sschema
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'schema') && $val_id !== '') $this->sschema = (string)$val_id; // evitem SQL injection fent cast a string
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->sschema = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('schema' => $this->sschema);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('db_idschema');
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
		if ($this->DBCarregar('guardar') === false) { $bInsert=true; } else { $bInsert=false; }
		$aDades=array();
		$aDades['id'] = $this->iid;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id                       = :id";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE schema='$this->sschema'")) === false) {
				$sClauError = 'DbSchema.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'DbSchema.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->sschema);
			$campos="(schema,id)";
			$valores="(:schema,:id)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'DbSchema.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'DbSchema.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		}
		$this->setAllAtributes($aDades);
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->sschema)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE schema='$this->sschema'")) === false) {
				$sClauError = 'DbSchema.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			// Para evitar posteriores cargas
			$this->bLoaded = TRUE;
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return false;
					break;
				default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return true;
		} else {
		   	return false;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE schema='$this->sschema'")) === false) {
			$sClauError = 'DbSchema.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return true;
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
		if (array_key_exists('schema',$aDades)) $this->setSchema($aDades['schema']);
		if (array_key_exists('id',$aDades)) $this->setId($aDades['id']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setSchema('');
		$this->setId('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de DbSchema en un array
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
	 * Recupera las claus primàries de DbSchema en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('schema' => $this->sschema);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de DbSchema en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'schema') && $val_id !== '') $this->sschema = $val_id;
	        }
	    }
	}

	/**
	 * Recupera l'atribut sschema de DbSchema
	 *
	 * @return string sschema
	 */
	function getSchema() {
		if (!isset($this->sschema) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sschema;
	}
	/**
	 * estableix el valor de l'atribut sschema de DbSchema
	 *
	 * @param string sschema
	 */
	function setSchema($sschema) {
		$this->sschema = $sschema;
	}
	/**
	 * Recupera l'atribut iid de DbSchema
	 *
	 * @return integer iid
	 */
	function getId() {
		if (!isset($this->iid) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid;
	}
	/**
	 * estableix el valor de l'atribut iid de DbSchema
	 *
	 * @param integer iid='' optional
	 */
	function setId($iid='') {
		$this->iid = $iid;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDbSchemaSet = new core\Set();

		$oDbSchemaSet->add($this->getDatosId());
		return $oDbSchemaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid de DbSchema
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id'));
		$oDatosCampo->setEtiqueta(_("id"));
		return $oDatosCampo;
	}
}
