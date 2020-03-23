<?php
namespace usuarios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula x_locales
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2014
 */
/**
 * Classe que implementa l'entitat x_locales
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2014
 */
class Local Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Local
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Local
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_locale de Local
	 *
	 * @var string
	 */
	 private $sid_locale;
	/**
	 * Nom Locale de Local
	 *
	 * @var string
	 */
	 private $snom_locale;
	/**
	 * Idioma de Local
	 *
	 * @var string
	 */
	 private $sidioma;
	/**
	 * Nom_idioma de Local
	 *
	 * @var string
	 */
	 private $snom_idioma;
	/**
	 * Activo de Local
	 *
	 * @var boolean
	 */
	 private $bactivo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Local
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Local
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
	 * @param integer|array sid_locale
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_locale') && $val_id !== '') $this->sid_locale = (string)$val_id; // evitem SQL injection fent cast a string
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->sid_locale = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_locale' => $this->sid_locale);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('x_locales');
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
		$aDades['nom_locale'] = $this->snom_locale;
		$aDades['idioma'] = $this->sidioma;
		$aDades['nom_idioma'] = $this->snom_idioma;
		$aDades['activo'] = $this->bactivo;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['activo']) ) { $aDades['activo']='true'; } else { $aDades['activo']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					nom_locale               = :nom_locale,
					idioma                   = :idioma,
					nom_idioma               = :nom_idioma,
					activo                   = :activo";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_locale='$this->sid_locale'")) === false) {
				$sClauError = 'Local.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Local.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->sid_locale);
			$campos="(id_locale,nom_locale,idioma,nom_idioma,activo)";
			$valores="(:id_locale,:nom_locale,:idioma,:nom_idioma,:activo)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Local.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Local.insertar.execute';
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
		if (isset($this->sid_locale)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_locale='$this->sid_locale'")) === false) {
				$sClauError = 'Local.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_locale='$this->sid_locale'")) === false) {
			$sClauError = 'Local.eliminar';
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
		if (array_key_exists('id_locale',$aDades)) $this->setId_locale($aDades['id_locale']);
		if (array_key_exists('nom_locale',$aDades)) $this->setNom_Locale($aDades['nom_locale']);
		if (array_key_exists('idioma',$aDades)) $this->setIdioma($aDades['idioma']);
		if (array_key_exists('nom_idioma',$aDades)) $this->setNom_idioma($aDades['nom_idioma']);
		if (array_key_exists('activo',$aDades)) $this->setActivo($aDades['activo']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_locale('');
		$this->setNom_Locale('');
		$this->setIdioma('');
		$this->setNom_idioma('');
		$this->setActivo('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Local en un array
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
	 * Recupera las claus primàries de Local en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_locale' => $this->sid_locale);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de Local en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_locale') && $val_id !== '') $this->slocale = $val_id;
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut sid_locale de Local
	 *
	 * @return string sid_locale
	 */
	function getId_locale() {
		if (!isset($this->sid_locale)) {
			$this->DBCarregar();
		}
		return $this->sid_locale;
	}
	/**
	 * estableix el valor de l'atribut sid_locale de Local
	 *
	 * @param string sid_locale
	 */
	function setId_locale($sid_locale) {
		$this->sid_locale = $sid_locale;
	}
	/**
	 * Recupera l'atribut snom_locale de Local
	 *
	 * @return string snom_locale
	 */
	function getNom_Locale() {
		if (!isset($this->snom_locale)) {
			$this->DBCarregar();
		}
		return $this->snom_locale;
	}
	/**
	 * estableix el valor de l'atribut snom_locale de Local
	 *
	 * @param string snom_locale='' optional
	 */
	function setNom_Locale($snom_locale='') {
		$this->snom_locale = $snom_locale;
	}
	/**
	 * Recupera l'atribut sidioma de Local
	 *
	 * @return string sidioma
	 */
	function getIdioma() {
		if (!isset($this->sidioma)) {
			$this->DBCarregar();
		}
		return $this->sidioma;
	}
	/**
	 * estableix el valor de l'atribut sidioma de Local
	 *
	 * @param string sidioma='' optional
	 */
	function setIdioma($sidioma='') {
		$this->sidioma = $sidioma;
	}
	/**
	 * Recupera l'atribut snom_idioma de Local
	 *
	 * @return string snom_idioma
	 */
	function getNom_idioma() {
		if (!isset($this->snom_idioma)) {
			$this->DBCarregar();
		}
		return $this->snom_idioma;
	}
	/**
	 * estableix el valor de l'atribut snom_idioma de Local
	 *
	 * @param string snom_idioma='' optional
	 */
	function setNom_idioma($snom_idioma='') {
		$this->snom_idioma = $snom_idioma;
	}
	/**
	 * Recupera l'atribut bactivo de Local
	 *
	 * @return boolean bactivo
	 */
	function getActivo() {
		if (!isset($this->bactivo)) {
			$this->DBCarregar();
		}
		return $this->bactivo;
	}
	/**
	 * estableix el valor de l'atribut bactivo de Local
	 *
	 * @param boolean bactivo='f' optional
	 */
	function setActivo($bactivo='f') {
		$this->bactivo = $bactivo;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oLocalSet = new core\Set();

		$oLocalSet->add($this->getDatosNom_Locale());
		$oLocalSet->add($this->getDatosIdioma());
		$oLocalSet->add($this->getDatosNom_idioma());
		$oLocalSet->add($this->getDatosActivo());
		return $oLocalSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snom_locale de Local
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_Locale() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_locale'));
		$oDatosCampo->setEtiqueta(_("nom_locale"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sidioma de Local
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosIdioma() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'idioma'));
		$oDatosCampo->setEtiqueta(_("idioma"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snom_idioma de Local
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_idioma() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_idioma'));
		$oDatosCampo->setEtiqueta(_("nombre idioma"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bactivo de Local
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosActivo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'activo'));
		$oDatosCampo->setEtiqueta(_("activo"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
}
?>
