<?php
namespace dbextern\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula conv_id_personas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/02/2017
 */
/**
 * Classe que implementa l'entitat conv_id_personas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/02/2017
 */
class IdMatchPersona Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de IdMatchPersona
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de IdMatchPersona
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_listas de IdMatchPersona
	 *
	 * @var integer
	 */
	 private $iid_listas;
	/**
	 * Id_orbix de IdMatchPersona
	 *
	 * @var integer
	 */
	 private $iid_orbix;
	/**
	 * Id_tabla de IdMatchPersona
	 *
	 * @var string
	 */
	 private $sid_tabla;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de IdMatchPersona
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de IdMatchPersona
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
	 * @param integer|array iid_listas
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_listas') && $val_id !== '') $this->iid_listas = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_listas = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_listas' => $this->iid_listas);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('conv_id_personas');
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
		$aDades['id_orbix'] = $this->iid_orbix;
		$aDades['id_tabla'] = $this->sid_tabla;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_orbix                 = :id_orbix,
					id_tabla                 = :id_tabla";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_listas='$this->iid_listas'")) === false) {
				$sClauError = 'IdMatchPersona.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'IdMatchPersona.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_listas);
			$campos="(id_listas,id_orbix,id_tabla)";
			$valores="(:id_listas,:id_orbix,:id_tabla)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'IdMatchPersona.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'IdMatchPersona.insertar.execute';
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
		if (isset($this->iid_listas)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_listas='$this->iid_listas'")) === false) {
				$sClauError = 'IdMatchPersona.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_listas='$this->iid_listas'")) === false) {
			$sClauError = 'IdMatchPersona.eliminar';
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
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_listas',$aDades)) $this->setId_listas($aDades['id_listas']);
		if (array_key_exists('id_orbix',$aDades)) $this->setId_orbix($aDades['id_orbix']);
		if (array_key_exists('id_tabla',$aDades)) $this->setId_tabla($aDades['id_tabla']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_listas('');
		$this->setId_orbix('');
		$this->setId_tabla('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de IdMatchPersona en un array
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
	 * Recupera las claus primàries de IdMatchPersona en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_listas' => $this->iid_listas);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Estableix las claus primàries de IdMatchPersona en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_listas') && $val_id !== '') $this->iid_listas = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_listas de IdMatchPersona
	 *
	 * @return integer iid_listas
	 */
	function getId_listas() {
		if (!isset($this->iid_listas)) {
			$this->DBCarregar();
		}
		return $this->iid_listas;
	}
	/**
	 * estableix el valor de l'atribut iid_listas de IdMatchPersona
	 *
	 * @param integer iid_listas
	 */
	function setId_listas($iid_listas) {
		$this->iid_listas = $iid_listas;
	}
	/**
	 * Recupera l'atribut iid_orbix de IdMatchPersona
	 *
	 * @return integer iid_orbix
	 */
	function getId_orbix() {
		if (!isset($this->iid_orbix)) {
			$this->DBCarregar();
		}
		return $this->iid_orbix;
	}
	/**
	 * estableix el valor de l'atribut iid_orbix de IdMatchPersona
	 *
	 * @param integer iid_orbix='' optional
	 */
	function setId_orbix($iid_orbix='') {
		$this->iid_orbix = $iid_orbix;
	}
	/**
	 * Recupera l'atribut sid_tabla de IdMatchPersona
	 *
	 * @return string sid_tabla
	 */
	function getId_tabla() {
		if (!isset($this->sid_tabla)) {
			$this->DBCarregar();
		}
		return $this->sid_tabla;
	}
	/**
	 * estableix el valor de l'atribut sid_tabla de IdMatchPersona
	 *
	 * @param string sid_tabla='' optional
	 */
	function setId_tabla($sid_tabla='') {
		$this->sid_tabla = $sid_tabla;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oIdMatchPersonaSet = new core\Set();

		$oIdMatchPersonaSet->add($this->getDatosId_orbix());
		$oIdMatchPersonaSet->add($this->getDatosId_tabla());
		return $oIdMatchPersonaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_orbix de IdMatchPersona
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_orbix() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_orbix'));
		$oDatosCampo->setEtiqueta(_("id_orbix"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sid_tabla de IdMatchPersona
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tabla() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tabla'));
		$oDatosCampo->setEtiqueta(_("id_tabla"));
		return $oDatosCampo;
	}
}
?>