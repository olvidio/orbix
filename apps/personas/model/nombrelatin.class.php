<?php
namespace personas\model;
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
				if (($nom_id == 'nom') && $val_id !== '') $this->snom = (string)$val_id; // evitem SQL injection fent cast a string
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->snom = (string)$a_id; // evitem SQL injection fent cast a string
				$this->aPrimary_key = array('snom' => $this->snom);
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
		$aDades['nominativo'] = $this->snominativo;
		$aDades['genitivo'] = $this->sgenitivo;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					nominativo               = :nominativo,
					genitivo                 = :genitivo";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE nom='$this->snom'")) === false) {
				$sClauError = 'NombreLatin.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'NombreLatin.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->snom);
			$campos="(nom,nominativo,genitivo)";
			$valores="(:nom,:nominativo,:genitivo)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'NombreLatin.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'NombreLatin.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
		if (isset($this->snom)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE nom='$this->snom'")) === false) {
				$sClauError = 'NombreLatin.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$qRs->rowCount()) return false;
					break;
				default:
					$this->setAllAtributes($aDades);
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE nom='$this->snom'")) === false) {
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
			$this->aPrimary_key = array('snom' => $this->snom);
		}
		return $this->aPrimary_key;
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

		$oNombreLatinSet->add($this->getDatosNominativo());
		$oNombreLatinSet->add($this->getDatosGenitivo());
		return $oNombreLatinSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snominativo de NombreLatin
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
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
	 * @return oject DatosCampo
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
