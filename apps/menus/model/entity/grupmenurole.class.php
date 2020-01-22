<?php
namespace menus\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/01/2014
 */
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/01/2014
 */
class GrupMenuRole Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de GrupMenuRole
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de GrupMenuRole
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de GrupMenuRole
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_grupmenu de GrupMenuRole
	 *
	 * @var integer
	 */
	 private $iid_grupmenu;
	/**
	 * Id_role de GrupMenuRole
	 *
	 * @var integer
	 */
	 private $iid_role;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
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
		$this->setNomTabla('aux_grupmenu_rol');
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
		$aDades['id_grupmenu'] = $this->iid_grupmenu;
		$aDades['id_role'] = $this->iid_role;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_grupmenu              = :id_grupmenu,
					id_role                  = :id_role";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'GrupMenuRole.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'GrupMenuRole.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(id_grupmenu,id_role)";
			$valores="(:id_grupmenu,:id_role)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'GrupMenuRole.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'GrupMenuRole.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_item = $oDbl->lastInsertId($nom_tabla.'_id_item_seq');
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
				$sClauError = 'GrupMenuRole.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
			$sClauError = 'GrupMenuRole.eliminar';
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_grupmenu',$aDades)) $this->setId_grupmenu($aDades['id_grupmenu']);
		if (array_key_exists('id_role',$aDades)) $this->setId_role($aDades['id_role']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_item('');
		$this->setId_grupmenu('');
		$this->setId_role('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de GrupMenuRole en un array
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
	 * Recupera las claus primàries de GrupMenuRole en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de GrupMenuRole
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
	 * estableix el valor de l'atribut iid_item de GrupMenuRole
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_grupmenu de GrupMenuRole
	 *
	 * @return integer iid_grupmenu
	 */
	function getId_grupmenu() {
		if (!isset($this->iid_grupmenu)) {
			$this->DBCarregar();
		}
		return $this->iid_grupmenu;
	}
	/**
	 * estableix el valor de l'atribut iid_grupmenu de GrupMenuRole
	 *
	 * @param integer iid_grupmenu='' optional
	 */
	function setId_grupmenu($iid_grupmenu='') {
		$this->iid_grupmenu = $iid_grupmenu;
	}
	/**
	 * Recupera l'atribut iid_role de GrupMenuRole
	 *
	 * @return integer iid_role
	 */
	function getId_role() {
		if (!isset($this->iid_role)) {
			$this->DBCarregar();
		}
		return $this->iid_role;
	}
	/**
	 * estableix el valor de l'atribut iid_role de GrupMenuRole
	 *
	 * @param integer iid_role='' optional
	 */
	function setId_role($iid_role='') {
		$this->iid_role = $iid_role;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oGrupMenuRoleSet = new core\Set();

		$oGrupMenuRoleSet->add($this->getDatosId_grupmenu());
		$oGrupMenuRoleSet->add($this->getDatosId_role());
		return $oGrupMenuRoleSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_grupmenu de GrupMenuRole
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_grupmenu() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_grupmenu'));
		$oDatosCampo->setEtiqueta(_("id_grupmenu"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_role de GrupMenuRole
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_role() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_role'));
		$oDatosCampo->setEtiqueta(_("id_role"));
		return $oDatosCampo;
	}
}
?>
