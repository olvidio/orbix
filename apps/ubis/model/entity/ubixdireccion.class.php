<?php
namespace ubis\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula u_cross_ubi_dir
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */
/**
 * Classe que implementa l'entitat u_cross_ubi_dir
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */
Abstract class UbixDireccion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de UbixDireccion
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de UbixDireccion
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_ubi de UbixDireccion
	 *
	 * @var integer
	 */
	 protected $iid_ubi;
	/**
	 * Id_direccion de UbixDireccion
	 *
	 * @var integer
	 */
	 protected $iid_direccion;
	/**
	 * Propietario de UbixDireccion
	 *
	 * @var boolean
	 */
	 protected $bpropietario;
	/**
	 * Principal de UbixDireccion
	 *
	 * @var boolean
	 */
	 protected $bprincipal;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_ubi,iid_direccion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
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
		$aDades['propietario'] = $this->bpropietario;
		$aDades['principal'] = $this->bprincipal;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['propietario']) ) { $aDades['propietario']='true'; } else { $aDades['propietario']='false'; }
		if ( core\is_true($aDades['principal']) ) { $aDades['principal']='true'; } else { $aDades['principal']='false'; }
		
		if ($bInsert === false) {
			//UPDATE
			$update="
					propietario              = :propietario,
					principal         	     = :principal";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi' AND id_direccion='$this->iid_direccion'")) === false) {
				$sClauError = 'UbixDireccion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'UbixDireccion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_ubi, $this->iid_direccion);
			$campos="(id_ubi,id_direccion,propietario,principal)";
			$valores="(:id_ubi,:id_direccion,:propietario,:principal)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'UbixDireccion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'UbixDireccion.insertar.execute';
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
		if (isset($this->iid_ubi) && isset($this->iid_direccion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi' AND id_direccion='$this->iid_direccion'")) === false) {
				$sClauError = 'UbixDireccion.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi' AND id_direccion='$this->iid_direccion'")) === false) {
			$sClauError = 'UbixDireccion.eliminar';
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
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('id_direccion',$aDades)) $this->setId_direccion($aDades['id_direccion']);
		if (array_key_exists('propietario',$aDades)) $this->setPropietario($aDades['propietario']);
		if (array_key_exists('principal',$aDades)) $this->setPrincipal($aDades['principal']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_ubi('');
		$this->setId_direccion('');
		$this->setPropietario('');
		$this->setPrincipal('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de UbixDireccion en un array
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
	 * Recupera las claus primàries de UbixDireccion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_ubi' => $this->iid_ubi,'id_direccion' => $this->iid_direccion);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de UbixDireccion en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'id_direccion') && $val_id !== '') $this->iid_direccion = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_ubi de UbixDireccion
	 *
	 * @return integer iid_ubi
	 */
	function getId_ubi() {
		if (!isset($this->iid_ubi)) {
			$this->DBCarregar();
		}
		return $this->iid_ubi;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi de UbixDireccion
	 *
	 * @param integer iid_ubi
	 */
	function setId_ubi($iid_ubi) {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut iid_direccion de UbixDireccion
	 *
	 * @return integer iid_direccion
	 */
	function getId_direccion() {
		if (!isset($this->iid_direccion)) {
			$this->DBCarregar();
		}
		return $this->iid_direccion;
	}
	/**
	 * estableix el valor de l'atribut iid_direccion de UbixDireccion
	 *
	 * @param integer iid_direccion
	 */
	function setId_direccion($iid_direccion) {
		$this->iid_direccion = $iid_direccion;
	}
	/**
	 * Recupera l'atribut bpropietario de UbixDireccion
	 *
	 * @return boolean bpropietario
	 */
	function getPropietario() {
		if (!isset($this->bpropietario)) {
			$this->DBCarregar();
		}
		return $this->bpropietario;
	}
	/**
	 * estableix el valor de l'atribut bpropietario de UbixDireccion
	 *
	 * @param boolean bpropietario='f' optional
	 */
	function setPropietario($bpropietario='f') {
		$this->bpropietario = $bpropietario;
	}
	/**
	 * Recupera l'atribut bprincipal de UbixDireccion
	 *
	 * @return boolean bprincipal
	 */
	function getPrincipal() {
		if (!isset($this->bprincipal)) {
			$this->DBCarregar();
		}
		return $this->bprincipal;
	}
	/**
	 * estableix el valor de l'atribut bprincipal de UbixDireccion
	 *
	 * @param boolean bprincipal='f' optional
	 */
	function setPrincipal($bprincipal='f') {
		$this->bprincipal = $bprincipal;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oUbixDireccionSet = new core\Set();

		$oUbixDireccionSet->add($this->getDatosPropietario());
		$oUbixDireccionSet->add($this->getDatosPrincipal());
		return $oUbixDireccionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut bpropietario de UbixDireccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPropietario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'propietario'));
		$oDatosCampo->setEtiqueta(_("propietario"));
		return $oDatosCampo;
	}

	/**
	 * Recupera les propietats de l'atribut bprincipal de UbixDireccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPrincipal() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'principal'));
		$oDatosCampo->setEtiqueta(_("principal"));
		return $oDatosCampo;
	}
}
?>
