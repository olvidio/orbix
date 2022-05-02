<?php
namespace documentos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula doc_whereis
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
/**
 * Classe que implementa l'entitat doc_whereis
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
class Whereis Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Whereis
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Whereis
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded de Whereis
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de Whereis
	 *
	 * @var integer
	 */
	 private $iid_schema;

	/**
	 * Id_item_whereis de Whereis
	 *
	 * @var integer
	 */
	 private $iid_item_whereis;
	/**
	 * Id_item_egm de Whereis
	 *
	 * @var integer
	 */
	 private $iid_item_egm;
	/**
	 * Id_doc de Whereis
	 *
	 * @var integer
	 */
	 private $iid_doc;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Whereis
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Whereis
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
	 * @param integer|array iid_item_whereis
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item_whereis') && $val_id !== '') $this->iid_item_whereis = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item_whereis = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item_whereis' => $this->iid_item_whereis);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('doc_whereis');
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
		$aDades['id_item_egm'] = $this->iid_item_egm;
		$aDades['id_doc'] = $this->iid_doc;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_item_egm              = :id_item_egm,
					id_doc                   = :id_doc";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item_whereis='$this->iid_item_whereis'")) === FALSE) {
				$sClauError = 'Whereis.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Whereis.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_item_egm,id_doc)";
			$valores="(:id_item_egm,:id_doc)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Whereis.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Whereis.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item_whereis = $oDbl->lastInsertId('doc_whereis_id_item_whereis_seq');
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
		if (isset($this->iid_item_whereis)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item_whereis='$this->iid_item_whereis'")) === FALSE) {
				$sClauError = 'Whereis.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item_whereis='$this->iid_item_whereis'")) === FALSE) {
			$sClauError = 'Whereis.eliminar';
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
		if (array_key_exists('id_item_whereis',$aDades)) $this->setId_item_whereis($aDades['id_item_whereis']);
		if (array_key_exists('id_item_egm',$aDades)) $this->setId_item_egm($aDades['id_item_egm']);
		if (array_key_exists('id_doc',$aDades)) $this->setId_doc($aDades['id_doc']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_item_whereis('');
		$this->setId_item_egm('');
		$this->setId_doc('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Whereis en un array
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
	 * Recupera las claus primàries de Whereis en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item_whereis' => $this->iid_item_whereis);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de Whereis en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item_whereis') && $val_id !== '') $this->iid_item_whereis = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item_whereis = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item_whereis' => $this->iid_item_whereis);
			}
		}
	}
	

	/**
	 * Recupera l'atribut iid_item_whereis de Whereis
	 *
	 * @return integer iid_item_whereis
	 */
	function getId_item_whereis() {
		if (!isset($this->iid_item_whereis) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_item_whereis;
	}
	/**
	 * estableix el valor de l'atribut iid_item_whereis de Whereis
	 *
	 * @param integer iid_item_whereis
	 */
	function setId_item_whereis($iid_item_whereis) {
		$this->iid_item_whereis = $iid_item_whereis;
	}
	/**
	 * Recupera l'atribut iid_item_egm de Whereis
	 *
	 * @return integer iid_item_egm
	 */
	function getId_item_egm() {
		if (!isset($this->iid_item_egm) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_item_egm;
	}
	/**
	 * estableix el valor de l'atribut iid_item_egm de Whereis
	 *
	 * @param integer iid_item_egm='' optional
	 */
	function setId_item_egm($iid_item_egm='') {
		$this->iid_item_egm = $iid_item_egm;
	}
	/**
	 * Recupera l'atribut iid_doc de Whereis
	 *
	 * @return integer iid_doc
	 */
	function getId_doc() {
		if (!isset($this->iid_doc) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_doc;
	}
	/**
	 * estableix el valor de l'atribut iid_doc de Whereis
	 *
	 * @param integer iid_doc='' optional
	 */
	function setId_doc($iid_doc='') {
		$this->iid_doc = $iid_doc;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oWhereisSet = new core\Set();

		$oWhereisSet->add($this->getDatosId_item_egm());
		$oWhereisSet->add($this->getDatosId_doc());
		return $oWhereisSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_item_egm de Whereis
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_item_egm() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_item_egm'));
		$oDatosCampo->setEtiqueta(_("id_item_egm"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_doc de Whereis
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_doc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_doc'));
		$oDatosCampo->setEtiqueta(_("id_doc"));
		return $oDatosCampo;
	}
}
