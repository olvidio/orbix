<?php
namespace ubis\model;
use core;
/**
 * Classe que implementa l'entitat xu_tipo_ctr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class TipoCentro Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoCentro
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoCentro
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Tipo_ctr de TipoCentro
	 *
	 * @var string
	 */
	 private $stipo_ctr;
	/**
	 * Nombre_tipo_ctr de TipoCentro
	 *
	 * @var string
	 */
	 private $snombre_tipo_ctr;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array stipo_ctr
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id === 'tipo_ctr') && $val_id !== '') $this->stipo_ctr = (string)$val_id;
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xu_tipo_ctr');
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
		$aDades['nombre_tipo_ctr'] = $this->snombre_tipo_ctr;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					nombre_tipo_ctr          = :nombre_tipo_ctr";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tipo_ctr='$this->stipo_ctr'")) === false) {
				$sClauError = 'TipoCentro.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'TipoCentro.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->stipo_ctr);
			$campos="(tipo_ctr,nombre_tipo_ctr)";
			$valores="(:tipo_ctr,:nombre_tipo_ctr)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TipoCentro.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'TipoCentro.insertar.execute';
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
		if (isset($this->stipo_ctr)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE tipo_ctr='$this->stipo_ctr'")) === false) {
				$sClauError = 'TipoCentro.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE tipo_ctr='$this->stipo_ctr'")) === false) {
			$sClauError = 'TipoCentro.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (array_key_exists('tipo_ctr',$aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
		if (array_key_exists('nombre_tipo_ctr',$aDades)) $this->setNombre_tipo_ctr($aDades['nombre_tipo_ctr']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de TipoCentro en un array
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
	 * Recupera las claus primàries de TipoCentro en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('stipo_ctr' => $this->stipo_ctr);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut stipo_ctr de TipoCentro
	 *
	 * @return string stipo_ctr
	 */
	function getTipo_ctr() {
		if (!isset($this->stipo_ctr)) {
			$this->DBCarregar();
		}
		return $this->stipo_ctr;
	}
	/**
	 * estableix el valor de l'atribut stipo_ctr de TipoCentro
	 *
	 * @param string stipo_ctr
	 */
	function setTipo_ctr($stipo_ctr) {
		$this->stipo_ctr = $stipo_ctr;
	}
	/**
	 * Recupera l'atribut snombre_tipo_ctr de TipoCentro
	 *
	 * @return string snombre_tipo_ctr
	 */
	function getNombre_tipo_ctr() {
		if (!isset($this->snombre_tipo_ctr)) {
			$this->DBCarregar();
		}
		return $this->snombre_tipo_ctr;
	}
	/**
	 * estableix el valor de l'atribut snombre_tipo_ctr de TipoCentro
	 *
	 * @param string snombre_tipo_ctr='' optional
	 */
	function setNombre_tipo_ctr($snombre_tipo_ctr='') {
		$this->snombre_tipo_ctr = $snombre_tipo_ctr;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTipoCentroSet = new core\Set();

		$oTipoCentroSet->add($this->getDatosTipo_ctr());
		$oTipoCentroSet->add($this->getDatosNombre_tipo_ctr());
		return $oTipoCentroSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut stipo_ctr de TipoCentro
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo_ctr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_ctr'));
		$oDatosCampo->setEtiqueta(_("tipo centro"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(6);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snombre_tipo_ctr de TipoCentro
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNombre_tipo_ctr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_tipo_ctr'));
		$oDatosCampo->setEtiqueta(_("nombre de tipo centro"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
}
?>
