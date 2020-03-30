<?php
namespace personas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula xe_nombre_latin
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
/**
 * Classe que implementa l'entitat xe_nombre_latin
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class NombreLatin Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de NombreLatin
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de NombreLatin
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de NombreLatin
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Nom de NombreLatin
	 *
	 * @var string
	 */
	 private $snom;
	/**
	 * Nominativo de NombreLatin
	 *
	 * @var string
	 */
	 private $snominativo;
	/**
	 * Genitivo de NombreLatin
	 *
	 * @var string
	 */
	 private $sgenitivo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de NombreLatin
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de NombreLatin
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
	 * @param integer|array snom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a string
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = (int)$a_id; // evitem SQL injection fent cast a string
				$this->aPrimary_key = array('id_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xe_nombre_latin');
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
		$aDades['nom'] = $this->snom;
		$aDades['nominativo'] = $this->snominativo;
		$aDades['genitivo'] = $this->sgenitivo;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					nom               		 = :nom,
					nominativo               = :nominativo,
					genitivo                 = :genitivo";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'NombreLatin.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'NombreLatin.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_item);
			$campos="(nom,nominativo,genitivo)";
			$valores="(:nom,:nominativo,:genitivo)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'NombreLatin.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'NombreLatin.insertar.execute';
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
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'NombreLatin.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
			$sClauError = 'NombreLatin.eliminar';
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
		if (array_key_exists('nom',$aDades)) $this->setNom($aDades['nom']);
		if (array_key_exists('nominativo',$aDades)) $this->setNominativo($aDades['nominativo']);
		if (array_key_exists('genitivo',$aDades)) $this->setGenitivo($aDades['genitivo']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setNom('');
		$this->setNominativo('');
		$this->setGenitivo('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de NombreLatin en un array
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
	 * Recupera las claus primàries de NombreLatin en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de NombreLatin en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut snom de NombreLatin
	 *
	 * @return string snom
	 */
	function getId_item() {
		if (!isset($this->iid_item)) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * Recupera l'atribut snom de NombreLatin
	 *
	 * @return string snom
	 */
	function getNom() {
		if (!isset($this->snom)) {
			$this->DBCarregar();
		}
		return $this->snom;
	}
	/**
	 * estableix el valor de l'atribut snom de NombreLatin
	 *
	 * @param string snom
	 */
	function setNom($snom) {
		$this->snom = $snom;
	}
	/**
	 * Recupera l'atribut snominativo de NombreLatin
	 *
	 * @return string snominativo
	 */
	function getNominativo() {
		if (!isset($this->snominativo)) {
			$this->DBCarregar();
		}
		return $this->snominativo;
	}
	/**
	 * estableix el valor de l'atribut snominativo de NombreLatin
	 *
	 * @param string snominativo='' optional
	 */
	function setNominativo($snominativo='') {
		$this->snominativo = $snominativo;
	}
	/**
	 * Recupera l'atribut sgenitivo de NombreLatin
	 *
	 * @return string sgenitivo
	 */
	function getGenitivo() {
		if (!isset($this->sgenitivo)) {
			$this->DBCarregar();
		}
		return $this->sgenitivo;
	}
	/**
	 * estableix el valor de l'atribut sgenitivo de NombreLatin
	 *
	 * @param string sgenitivo='' optional
	 */
	function setGenitivo($sgenitivo='') {
		$this->sgenitivo = $sgenitivo;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oNombreLatinSet = new core\Set();

		$oNombreLatinSet->add($this->getDatosNom());
		$oNombreLatinSet->add($this->getDatosNominativo());
		$oNombreLatinSet->add($this->getDatosGenitivo());
		return $oNombreLatinSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snom de NombreLatin
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom'));
		$oDatosCampo->setEtiqueta(_("vernácula"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snominativo de NombreLatin
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNominativo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nominativo'));
		$oDatosCampo->setEtiqueta(_("nominativo"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sgenitivo de NombreLatin
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosGenitivo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'genitivo'));
		$oDatosCampo->setEtiqueta(_("genitivo"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
}
?>
