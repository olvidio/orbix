<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat xu_tipo_casa
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class TipoCasa Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoCasa
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoCasa
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Tipo_casa de TipoCasa
	 *
	 * @var string
	 */
	 private $stipo_casa;
	/**
	 * Nombre_tipo_casa de TipoCasa
	 *
	 * @var string
	 */
	 private $snombre_tipo_casa;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array stipo_casa
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id === 'tipo_casa') && $val_id !== '') $this->stipo_casa = (string)$val_id; 
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->stipo_casa = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('stipo_casa' => $this->stipo_casa);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xu_tipo_casa');
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
		$aDades['nombre_tipo_casa'] = $this->snombre_tipo_casa;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					nombre_tipo_casa         = :nombre_tipo_casa";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tipo_casa='$this->stipo_casa'")) === false) {
				$sClauError = 'TipoCasa.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoCasa.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->stipo_casa);
			$campos="(tipo_casa,nombre_tipo_casa)";
			$valores="(:tipo_casa,:nombre_tipo_casa)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TipoCasa.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoCasa.insertar.execute';
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
		if (isset($this->stipo_casa)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE tipo_casa='$this->stipo_casa'")) === false) {
				$sClauError = 'TipoCasa.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE tipo_casa='$this->stipo_casa'")) === false) {
			$sClauError = 'TipoCasa.eliminar';
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
		if (array_key_exists('tipo_casa',$aDades)) $this->setTipo_casa($aDades['tipo_casa']);
		if (array_key_exists('nombre_tipo_casa',$aDades)) $this->setNombre_tipo_casa($aDades['nombre_tipo_casa']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de TipoCasa en un array
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
	 * Recupera las claus primàries de TipoCasa en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('stipo_casa' => $this->stipo_casa);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut stipo_casa de TipoCasa
	 *
	 * @return string stipo_casa
	 */
	function getTipo_casa() {
		if (!isset($this->stipo_casa)) {
			$this->DBCarregar();
		}
		return $this->stipo_casa;
	}
	/**
	 * estableix el valor de l'atribut stipo_casa de TipoCasa
	 *
	 * @param string stipo_casa
	 */
	function setTipo_casa($stipo_casa) {
		$this->stipo_casa = $stipo_casa;
	}
	/**
	 * Recupera l'atribut snombre_tipo_casa de TipoCasa
	 *
	 * @return string snombre_tipo_casa
	 */
	function getNombre_tipo_casa() {
		if (!isset($this->snombre_tipo_casa)) {
			$this->DBCarregar();
		}
		return $this->snombre_tipo_casa;
	}
	/**
	 * estableix el valor de l'atribut snombre_tipo_casa de TipoCasa
	 *
	 * @param string snombre_tipo_casa='' optional
	 */
	function setNombre_tipo_casa($snombre_tipo_casa='') {
		$this->snombre_tipo_casa = $snombre_tipo_casa;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTipoDeCasaSet = new core\Set();

		$oTipoDeCasaSet->add($this->getDatosTipo_casa());
		$oTipoDeCasaSet->add($this->getDatosNombre_tipo_casa());
		return $oTipoDeCasaSet->getTot();
	}


	/**
	 * Recupera les propietats de l'atribut stipo_casa de TipoDeCasa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_casa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_casa'));
		$oDatosCampo->setEtiqueta(_("tipo de casa"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(6);
		return $oDatosCampo;
	}

	/**
	 * Recupera les propietats de l'atribut snombre_tipo_casa de TipoDeCasa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNombre_tipo_casa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_tipo_casa'));
		$oDatosCampo->setEtiqueta(_("nombre del tipo de casa"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
}
?>
