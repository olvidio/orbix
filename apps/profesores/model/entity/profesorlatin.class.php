<?php
namespace profesores\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_profesor_latin
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */
/**
 * Classe que implementa l'entitat d_profesor_latin
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */
class ProfesorLatin Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ProfesorLatin
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ProfesorLatin
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_nom de ProfesorLatin
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Latin de ProfesorLatin
	 *
	 * @var boolean
	 */
	 private $blatin;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ProfesorLatin
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ProfesorLatin
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
	 * @param integer|array iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_nom = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_nom' => $this->iid_nom);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_profesor_latin');
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
		$aDades['latin'] = $this->blatin;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['latin'] = ($aDades['latin'] === 't')? 'true' : $aDades['latin'];
		if ( filter_var( $aDades['latin'], FILTER_VALIDATE_BOOLEAN)) { $aDades['latin']='t'; } else { $aDades['latin']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					latin                    = :latin";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom")) === false) {
				$sClauError = 'ProfesorLatin.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorLatin.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_nom);
			$campos="(id_nom,latin)";
			$valores="(:id_nom,:latin)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ProfesorLatin.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorLatin.insertar.execute';
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
		if (isset($this->iid_nom)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
				$sClauError = 'ProfesorLatin.carregar';
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
				default:					// En el caso de no existir esta fila, $aDades = FALSE:					if ($aDades === FALSE) {
						$this->setNullAllAtributes();					} else {						$this->setAllAtributes($aDades);					}			}
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
			$sClauError = 'ProfesorLatin.eliminar';
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
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('latin',$aDades)) $this->setLatin($aDades['latin']);
	}	/**	 * Estableix a empty el valor de tots els atributs	 *	 */	function setNullAllAtributes() {
		$this->setId_schema('');
		$this->setId_nom('');
		$this->setLatin('');
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ProfesorLatin en un array
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
	 * Recupera las claus primàries de ProfesorLatin en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_nom' => $this->iid_nom);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_nom de ProfesorLatin
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		if (!isset($this->iid_nom)) {
			$this->DBCarregar();
		}
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de ProfesorLatin
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut blatin de ProfesorLatin
	 *
	 * @return boolean blatin
	 */
	function getLatin() {
		if (!isset($this->blatin)) {
			$this->DBCarregar();
		}
		return $this->blatin;
	}
	/**
	 * estableix el valor de l'atribut blatin de ProfesorLatin
	 *
	 * @param boolean blatin='f' optional
	 */
	function setLatin($blatin='f') {
		$this->blatin = $blatin;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oProfesorLatinSet = new core\Set();

		$oProfesorLatinSet->add($this->getDatosLatin());
		return $oProfesorLatinSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut blatin de ProfesorLatin
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLatin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'latin'));
		$oDatosCampo->setEtiqueta(_("latín"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
}
?>
