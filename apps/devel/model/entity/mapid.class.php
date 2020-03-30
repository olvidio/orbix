<?php
namespace devel\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula map_id
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 9/3/2020
 */
/**
 * Classe que implementa l'entitat map_id
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 9/3/2020
 */
class MapId Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de MapId
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de MapId
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Objeto de MapId
	 *
	 * @var string
	 */
	 private $sobjeto;
	/**
	 * Id_resto de MapId
	 *
	 * @var integer
	 */
	 private $iid_resto;
	/**
	 * Id_dl de MapId
	 *
	 * @var integer
	 */
	 private $iid_dl;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de MapId
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de MapId
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
	 * @param integer|array sobjeto,iid_resto
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBRC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'objeto') && $val_id !== '') $this->sobjeto = (string)$val_id; // evitem SQL injection fent cast a string
				if (($nom_id == 'id_resto') && $val_id !== '') $this->iid_resto = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('map_id');
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
		$aDades['id_dl'] = $this->iid_dl;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_dl                    = :id_dl";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE objeto='$this->sobjeto' AND id_resto='$this->iid_resto'")) === FALSE) {
				$sClauError = 'MapId.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'MapId.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->sobjeto, $this->iid_resto);
			$campos="(objeto,id_resto,id_dl)";
			$valores="(:objeto,:id_resto,:id_dl)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'MapId.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'MapId.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
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
		if (isset($this->sobjeto) && isset($this->iid_resto)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE objeto='$this->sobjeto' AND id_resto='$this->iid_resto'")) === FALSE) {
				$sClauError = 'MapId.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE objeto='$this->sobjeto' AND id_resto='$this->iid_resto'")) === FALSE) {
			$sClauError = 'MapId.eliminar';
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
		if (array_key_exists('objeto',$aDades)) $this->setObjeto($aDades['objeto']);
		if (array_key_exists('id_resto',$aDades)) $this->setId_resto($aDades['id_resto']);
		if (array_key_exists('id_dl',$aDades)) $this->setId_dl($aDades['id_dl']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setObjeto('');
		$this->setId_resto('');
		$this->setId_dl('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de MapId en un array
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
	 * Recupera las claus primàries de MapId en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('objeto' => $this->sobjeto,'id_resto' => $this->iid_resto);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de MapId en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'objeto') && $val_id !== '') $this->sobjeto = (string)$val_id; // evitem SQL injection fent cast a string
				if (($nom_id == 'id_resto') && $val_id !== '') $this->iid_resto = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
	}
	

	/**
	 * Recupera l'atribut sobjeto de MapId
	 *
	 * @return string sobjeto
	 */
	function getObjeto() {
		if (!isset($this->sobjeto)) {
			$this->DBCarregar();
		}
		return $this->sobjeto;
	}
	/**
	 * estableix el valor de l'atribut sobjeto de MapId
	 *
	 * @param string sobjeto
	 */
	function setObjeto($sobjeto) {
		$this->sobjeto = $sobjeto;
	}
	/**
	 * Recupera l'atribut iid_resto de MapId
	 *
	 * @return integer iid_resto
	 */
	function getId_resto() {
		if (!isset($this->iid_resto)) {
			$this->DBCarregar();
		}
		return $this->iid_resto;
	}
	/**
	 * estableix el valor de l'atribut iid_resto de MapId
	 *
	 * @param integer iid_resto
	 */
	function setId_resto($iid_resto) {
		$this->iid_resto = $iid_resto;
	}
	/**
	 * Recupera l'atribut iid_dl de MapId
	 *
	 * @return integer iid_dl
	 */
	function getId_dl() {
		if (!isset($this->iid_dl)) {
			$this->DBCarregar();
		}
		return $this->iid_dl;
	}
	/**
	 * estableix el valor de l'atribut iid_dl de MapId
	 *
	 * @param integer iid_dl='' optional
	 */
	function setId_dl($iid_dl='') {
		$this->iid_dl = $iid_dl;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oMapIdSet = new core\Set();

		$oMapIdSet->add($this->getDatosId_dl());
		return $oMapIdSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_dl de MapId
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_dl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_dl'));
		$oDatosCampo->setEtiqueta(_("id_dl"));
		return $oDatosCampo;
	}
}
