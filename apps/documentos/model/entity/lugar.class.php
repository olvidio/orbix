<?php
namespace documentos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula doc_lugares
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
/**
 * Classe que implementa l'entitat doc_lugares
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
class Lugar Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Lugar
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Lugar
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded de Lugar
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de Lugar
	 *
	 * @var integer
	 */
	 private $iid_schema;

	/**
	 * Id_lugar de Lugar
	 *
	 * @var integer
	 */
	 private $iid_lugar;
	/**
	 * Id_ubi de Lugar
	 *
	 * @var integer
	 */
	 private $iid_ubi;
	/**
	 * Nom_lugar de Lugar
	 *
	 * @var string
	 */
	 private $snom_lugar;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Lugar
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Lugar
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
	 * @param integer|array iid_lugar
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_lugar') && $val_id !== '') $this->iid_lugar = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_lugar = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_lugar' => $this->iid_lugar);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('doc_lugares');
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
		$aDades['id_ubi'] = $this->iid_ubi;
		$aDades['nom_lugar'] = $this->snom_lugar;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_ubi                   = :id_ubi,
					nom_lugar                = :nom_lugar";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_lugar='$this->iid_lugar'")) === FALSE) {
				$sClauError = 'Lugar.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Lugar.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_ubi,nom_lugar)";
			$valores="(:id_ubi,:nom_lugar)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Lugar.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Lugar.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_lugar = $oDbl->lastInsertId('doc_lugares_id_lugar_seq');
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
		if (isset($this->iid_lugar)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_lugar='$this->iid_lugar'")) === FALSE) {
				$sClauError = 'Lugar.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_lugar='$this->iid_lugar'")) === FALSE) {
			$sClauError = 'Lugar.eliminar';
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
		if (array_key_exists('id_lugar',$aDades)) $this->setId_lugar($aDades['id_lugar']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('nom_lugar',$aDades)) $this->setNom_lugar($aDades['nom_lugar']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_lugar('');
		$this->setId_ubi('');
		$this->setNom_lugar('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Lugar en un array
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
	 * Recupera las claus primàries de Lugar en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_lugar' => $this->iid_lugar);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de Lugar en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_lugar') && $val_id !== '') $this->iid_lugar = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_lugar = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_lugar' => $this->iid_lugar);
			}
		}
	}
	

	/**
	 * Recupera l'atribut iid_lugar de Lugar
	 *
	 * @return integer iid_lugar
	 */
	function getId_lugar() {
		if (!isset($this->iid_lugar) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_lugar;
	}
	/**
	 * estableix el valor de l'atribut iid_lugar de Lugar
	 *
	 * @param integer iid_lugar
	 */
	function setId_lugar($iid_lugar) {
		$this->iid_lugar = $iid_lugar;
	}
	/**
	 * Recupera l'atribut iid_ubi de Lugar
	 *
	 * @return integer iid_ubi
	 */
	function getId_ubi() {
		if (!isset($this->iid_ubi) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_ubi;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi de Lugar
	 *
	 * @param integer iid_ubi='' optional
	 */
	function setId_ubi($iid_ubi='') {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut snom_lugar de Lugar
	 *
	 * @return string snom_lugar
	 */
	function getNom_lugar() {
		if (!isset($this->snom_lugar) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->snom_lugar;
	}
	/**
	 * estableix el valor de l'atribut snom_lugar de Lugar
	 *
	 * @param string snom_lugar='' optional
	 */
	function setNom_lugar($snom_lugar='') {
		$this->snom_lugar = $snom_lugar;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oLugarSet = new core\Set();

		$oLugarSet->add($this->getDatosId_ubi());
		$oLugarSet->add($this->getDatosNom_lugar());
		return $oLugarSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_ubi de Lugar
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi'));
		$oDatosCampo->setEtiqueta(_("centro/casa"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('documentos\model\entity\UbiDoc');
		$oDatosCampo->setArgument2('nom_ubi');
		$oDatosCampo->setArgument3('getListaUbisDoc');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snom_lugar de Lugar
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_lugar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_lugar'));
		$oDatosCampo->setEtiqueta(_("lugar"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
}
