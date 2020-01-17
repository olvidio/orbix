<?php
namespace personas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula xp_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/03/2014
 */
/**
 * Classe que implementa l'entitat xp_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/03/2014
 */
class Situacion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Situacion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Situacion
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Situacion de Situacion
	 *
	 * @var string
	 */
	 private $ssituacion;
	/**
	 * Nombre_situacion de Situacion
	 *
	 * @var string
	 */
	 private $snombre_situacion;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array ssituacion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'situacion') && $val_id !== '') $this->ssituacion = (string)$val_id; // evitem SQL injection fent cast a string
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->ssituacion = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('ssituacion' => $this->ssituacion);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xp_situacion');
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
		$aDades['nombre_situacion'] = $this->snombre_situacion;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					nombre_situacion         = :nombre_situacion";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE situacion='$this->ssituacion'")) === false) {
				$sClauError = 'Situacion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Situacion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->ssituacion);
			$campos="(situacion,nombre_situacion)";
			$valores="(:situacion,:nombre_situacion)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Situacion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Situacion.insertar.execute';
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
		if (isset($this->ssituacion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE situacion='$this->ssituacion'")) === false) {
				$sClauError = 'Situacion.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE situacion='$this->ssituacion'")) === false) {
			$sClauError = 'Situacion.eliminar';
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
		if (array_key_exists('situacion',$aDades)) $this->setSituacion($aDades['situacion']);
		if (array_key_exists('nombre_situacion',$aDades)) $this->setNombre_situacion($aDades['nombre_situacion']);
	}	/**	 * Estableix a empty el valor de tots els atributs	 *	 */	function setNullAllAtributes() {
		$this->setSituacion('');
		$this->setNombre_situacion('');
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Situacion en un array
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
	 * Recupera las claus primàries de Situacion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('ssituacion' => $this->ssituacion);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut ssituacion de Situacion
	 *
	 * @return string ssituacion
	 */
	function getSituacion() {
		if (!isset($this->ssituacion)) {
			$this->DBCarregar();
		}
		return $this->ssituacion;
	}
	/**
	 * estableix el valor de l'atribut ssituacion de Situacion
	 *
	 * @param string ssituacion
	 */
	function setSituacion($ssituacion) {
		$this->ssituacion = $ssituacion;
	}
	/**
	 * Recupera l'atribut snombre_situacion de Situacion
	 *
	 * @return string snombre_situacion
	 */
	function getNombre_situacion() {
		if (!isset($this->snombre_situacion)) {
			$this->DBCarregar();
		}
		return $this->snombre_situacion;
	}
	/**
	 * estableix el valor de l'atribut snombre_situacion de Situacion
	 *
	 * @param string snombre_situacion='' optional
	 */
	function setNombre_situacion($snombre_situacion='') {
		$this->snombre_situacion = $snombre_situacion;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oSituacionSet = new core\Set();

		$oSituacionSet->add($this->getDatosNombre_situacion());
		return $oSituacionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snombre_situacion de Situacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNombre_situacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_situacion'));
		$oDatosCampo->setEtiqueta(_("nombre situación"));
		return $oDatosCampo;
	}
}
