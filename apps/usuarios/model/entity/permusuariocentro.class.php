<?php
namespace usuarios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula aux_usuarios_ctr_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/5/2019
 */
/**
 * Classe que implementa l'entitat aux_usuarios_ctr_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/5/2019
 */
class PermUsuarioCentro Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de PermUsuarioCentro
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de PermUsuarioCentro
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de PermUsuarioCentro
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_usuario de PermUsuarioCentro
	 *
	 * @var integer
	 */
	 private $iid_usuario;
	/**
	 * Id_ctr de PermUsuarioCentro
	 *
	 * @var integer
	 */
	 private $iid_ctr;
	/**
	 * Perm_ctr de PermUsuarioCentro
	 *
	 * @var integer
	 */
	 private $iperm_ctr;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de PermUsuarioCentro
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de PermUsuarioCentro
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
	 * @param integer|array iid_item
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_usuarios_ctr_perm');
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
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['id_usuario'] = $this->iid_usuario;
		$aDades['id_ctr'] = $this->iid_ctr;
		$aDades['perm_ctr'] = $this->iperm_ctr;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_usuario               = :id_usuario,
					id_ctr                   = :id_ctr,
					perm_ctr                 = :perm_ctr";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'PermUsuarioCentro.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'PermUsuarioCentro.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_usuario,id_ctr,perm_ctr)";
			$valores="(:id_usuario,:id_ctr,:perm_ctr)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'PermUsuarioCentro.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'PermUsuarioCentro.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('aux_usuarios_ctr_perm_id_item_seq');
		}
		$this->setAllAtributes($aDades);
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'PermUsuarioCentro.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return FALSE;
					break;
				default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return TRUE;
		} else {
		   	return FALSE;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
			$sClauError = 'PermUsuarioCentro.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('id_ctr',$aDades)) $this->setId_ctr($aDades['id_ctr']);
		if (array_key_exists('perm_ctr',$aDades)) $this->setPerm_ctr($aDades['perm_ctr']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_item('');
		$this->setId_usuario('');
		$this->setId_ctr('');
		$this->setPerm_ctr('');
	}



	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de PermUsuarioCentro en un array
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
	 * Recupera las claus primàries de PermUsuarioCentro en un array
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
	 * Recupera l'atribut iid_item de PermUsuarioCentro
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item)) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de PermUsuarioCentro
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_usuario de PermUsuarioCentro
	 *
	 * @return integer iid_usuario
	 */
	function getId_usuario() {
		if (!isset($this->iid_usuario)) {
			$this->DBCarregar();
		}
		return $this->iid_usuario;
	}
	/**
	 * estableix el valor de l'atribut iid_usuario de PermUsuarioCentro
	 *
	 * @param integer iid_usuario='' optional
	 */
	function setId_usuario($iid_usuario='') {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut iid_ctr de PermUsuarioCentro
	 *
	 * @return integer iid_ctr
	 */
	function getId_ctr() {
		if (!isset($this->iid_ctr)) {
			$this->DBCarregar();
		}
		return $this->iid_ctr;
	}
	/**
	 * estableix el valor de l'atribut iid_ctr de PermUsuarioCentro
	 *
	 * @param integer iid_ctr='' optional
	 */
	function setId_ctr($iid_ctr='') {
		$this->iid_ctr = $iid_ctr;
	}
	/**
	 * Recupera l'atribut iperm_ctr de PermUsuarioCentro
	 *
	 * @return integer iperm_ctr
	 */
	function getPerm_ctr() {
		if (!isset($this->iperm_ctr)) {
			$this->DBCarregar();
		}
		return $this->iperm_ctr;
	}
	/**
	 * estableix el valor de l'atribut iperm_ctr de PermUsuarioCentro
	 *
	 * @param integer iperm_ctr='' optional
	 */
	function setPerm_ctr($iperm_ctr='') {
		$this->iperm_ctr = $iperm_ctr;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oPermUsuarioCentroSet = new core\Set();

		$oPermUsuarioCentroSet->add($this->getDatosId_usuario());
		$oPermUsuarioCentroSet->add($this->getDatosId_ctr());
		$oPermUsuarioCentroSet->add($this->getDatosPerm_ctr());
		return $oPermUsuarioCentroSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_usuario de PermUsuarioCentro
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_usuario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_usuario'));
		$oDatosCampo->setEtiqueta(_("id_usuario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_ctr de PermUsuarioCentro
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ctr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ctr'));
		$oDatosCampo->setEtiqueta(_("id_ctr"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iperm_ctr de PermUsuarioCentro
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPerm_ctr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'perm_ctr'));
		$oDatosCampo->setEtiqueta(_("perm_ctr"));
		return $oDatosCampo;
	}
}
