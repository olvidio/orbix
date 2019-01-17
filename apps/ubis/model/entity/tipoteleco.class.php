<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat xd_tipo_teleco
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class TipoTeleco Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoTeleco
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoTeleco
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Tipo_teleco de TipoTeleco
	 *
	 * @var string
	 */
	 private $stipo_teleco;
	/**
	 * Nombre_teleco de TipoTeleco
	 *
	 * @var string
	 */
	 private $snombre_teleco;
	/**
	 * Ubi de TipoTeleco
	 *
	 * @var boolean
	 */
	 private $bubi;
	/**
	 * Persona de TipoTeleco
	 *
	 * @var boolean
	 */
	 private $bpersona;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array stipo_teleco
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->stipo_teleco = $a_id;
				$this->aPrimary_key = array('stipo_teleco' => $this->stipo_teleco);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xd_tipo_teleco');
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
		$aDades['nombre_teleco'] = $this->snombre_teleco;
		$aDades['ubi'] = $this->bubi;
		$aDades['persona'] = $this->bpersona;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['ubi'] = ($aDades['ubi'] === 't')? 'true' : '';
		if ( filter_var( $aDades['ubi'], FILTER_VALIDATE_BOOLEAN)) { $aDades['ubi']='t'; } else { $aDades['ubi']='f'; }
		$aDades['persona'] = ($aDades['persona'] === 't')? 'true' : '';
		if ( filter_var( $aDades['persona'], FILTER_VALIDATE_BOOLEAN)) { $aDades['persona']='t'; } else { $aDades['persona']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					nombre_teleco            = :nombre_teleco,
					ubi                      = :ubi,
					persona                  = :persona";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tipo_teleco='$this->stipo_teleco'")) === false) {
				$sClauError = 'TipoTeleco.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoTeleco.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->stipo_teleco);
			$campos="(tipo_teleco,nombre_teleco,ubi,persona)";
			$valores="(:tipo_teleco,:nombre_teleco,:ubi,:persona)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TipoTeleco.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoTeleco.insertar.execute';
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
		if (isset($this->stipo_teleco)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE tipo_teleco='$this->stipo_teleco'")) === false) {
				$sClauError = 'TipoTeleco.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE tipo_teleco='$this->stipo_teleco'")) === false) {
			$sClauError = 'TipoTeleco.eliminar';
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
		if (array_key_exists('tipo_teleco',$aDades)) $this->setTipo_teleco($aDades['tipo_teleco']);
		if (array_key_exists('nombre_teleco',$aDades)) $this->setNombre_teleco($aDades['nombre_teleco']);
		if (array_key_exists('ubi',$aDades)) $this->setUbi($aDades['ubi']);
		if (array_key_exists('persona',$aDades)) $this->setPersona($aDades['persona']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de TipoTeleco en un array
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
	 * Recupera las claus primàries de TipoTeleco en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('stipo_teleco' => $this->stipo_teleco);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut stipo_teleco de TipoTeleco
	 *
	 * @return string stipo_teleco
	 */
	function getTipo_teleco() {
		if (!isset($this->stipo_teleco)) {
			$this->DBCarregar();
		}
		return $this->stipo_teleco;
	}
	/**
	 * estableix el valor de l'atribut stipo_teleco de TipoTeleco
	 *
	 * @param string stipo_teleco
	 */
	function setTipo_teleco($stipo_teleco) {
		$this->stipo_teleco = $stipo_teleco;
	}
	/**
	 * Recupera l'atribut snombre_teleco de TipoTeleco
	 *
	 * @return string snombre_teleco
	 */
	function getNombre_teleco() {
		if (!isset($this->snombre_teleco)) {
			$this->DBCarregar();
		}
		return $this->snombre_teleco;
	}
	/**
	 * estableix el valor de l'atribut snombre_teleco de TipoTeleco
	 *
	 * @param string snombre_teleco
	 */
	function setNombre_teleco($snombre_teleco) {
		$this->snombre_teleco = $snombre_teleco;
	}
	/**
	 * Recupera l'atribut bubi de TipoTeleco
	 *
	 * @return boolean bubi
	 */
	function getUbi() {
		if (!isset($this->bubi)) {
			$this->DBCarregar();
		}
		return $this->bubi;
	}
	/**
	 * estableix el valor de l'atribut bubi de TipoTeleco
	 *
	 * @param boolean bubi='f' optional
	 */
	function setUbi($bubi='f') {
		$this->bubi = $bubi;
	}
	/**
	 * Recupera l'atribut bpersona de TipoTeleco
	 *
	 * @return boolean bpersona
	 */
	function getPersona() {
		if (!isset($this->bpersona)) {
			$this->DBCarregar();
		}
		return $this->bpersona;
	}
	/**
	 * estableix el valor de l'atribut bpersona de TipoTeleco
	 *
	 * @param boolean bpersona='f' optional
	 */
	function setPersona($bpersona='f') {
		$this->bpersona = $bpersona;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTipoTelecoSet = new core\Set();

		$oTipoTelecoSet->add($this->getDatosUbi());
		$oTipoTelecoSet->add($this->getDatosPersona());
		return $oTipoTelecoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut bubi de TipoTeleco
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosUbi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ubi'));
		$oDatosCampo->setEtiqueta(_("ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpersona de TipoTeleco
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPersona() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'persona'));
		$oDatosCampo->setEtiqueta(_("persona"));
		return $oDatosCampo;
	}
}
?>
