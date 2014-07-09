<?php
namespace menus\model;
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
class GrupMenu Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de GrupMenu
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de GrupMenu
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_grupmenu de GrupMenu
	 *
	 * @var integer
	 */
	 private $iid_grupmenu;
	/**
	 * Grup_menu de GrupMenu
	 *
	 * @var string
	 */
	 private $sgrup_menu;
	/**
	 * Orden de GrupMenu
	 *
	 * @var integer
	 */
	 private $iorden;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_grupmenu
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_grupmenu') && $val_id !== '') $this->iid_grupmenu = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_grupmenu = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_grupmenu' => $this->iid_grupmenu);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_grupmenu');
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
		$aDades['grup_menu'] = $this->sgrup_menu;
		$aDades['orden'] = $this->iorden;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					grup_menu                = :grup_menu,
					orden                    = :orden";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_grupmenu='$this->iid_grupmenu'")) === false) {
				$sClauError = 'GrupMenu.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'GrupMenu.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(grup_menu,orden)";
			$valores="(:grup_menu,:orden)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'GrupMenu.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'GrupMenu.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_grupmenu = $oDbl->lastInsertId('$nom_tabla_id_gm_seq');
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
		if (isset($this->iid_grupmenu)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_grupmenu='$this->iid_grupmenu'")) === false) {
				$sClauError = 'GrupMenu.carregar';
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_grupmenu='$this->iid_grupmenu'")) === false) {
			$sClauError = 'GrupMenu.eliminar';
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
		if (array_key_exists('id_grupmenu',$aDades)) $this->setId_grupmenu($aDades['id_grupmenu']);
		if (array_key_exists('grup_menu',$aDades)) $this->setGrup_menu($aDades['grup_menu']);
		if (array_key_exists('orden',$aDades)) $this->setOrden($aDades['orden']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de GrupMenu en un array
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
	 * Recupera las claus primàries de GrupMenu en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_grupmenu' => $this->iid_grupmenu);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_grupmenu de GrupMenu
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
	 * estableix el valor de l'atribut iid_grupmenu de GrupMenu
	 *
	 * @param integer iid_grupmenu
	 */
	function setId_grupmenu($iid_grupmenu) {
		$this->iid_grupmenu = $iid_grupmenu;
	}
	/**
	 * Recupera l'atribut sgrup_menu de GrupMenu
	 *
	 * @return string sgrup_menu
	 */
	function getGrup_menu() {
		if (!isset($this->sgrup_menu)) {
			$this->DBCarregar();
		}
		return $this->sgrup_menu;
	}
	/**
	 * estableix el valor de l'atribut sgrup_menu de GrupMenu
	 *
	 * @param string sgrup_menu='' optional
	 */
	function setGrup_menu($sgrup_menu='') {
		$this->sgrup_menu = $sgrup_menu;
	}
	/**
	 * Recupera l'atribut iorden de GrupMenu
	 *
	 * @return integer iorden
	 */
	function getOrden() {
		if (!isset($this->iorden)) {
			$this->DBCarregar();
		}
		return $this->iorden;
	}
	/**
	 * estableix el valor de l'atribut iorden de GrupMenu
	 *
	 * @param integer iorden='' optional
	 */
	function setOrden($iorden='') {
		$this->iorden = $iorden;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oGrupMenuSet = new core\Set();

		$oGrupMenuSet->add($this->getDatosGrup_menu());
		$oGrupMenuSet->add($this->getDatosOrden());
		return $oGrupMenuSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut sgrup_menu de GrupMenu
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosGrup_menu() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'grup_menu'));
		$oDatosCampo->setEtiqueta(_("grup_menu"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iorden de GrupMenu
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosOrden() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'orden'));
		$oDatosCampo->setEtiqueta(_("orden"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(10);
		return $oDatosCampo;
	}
}
?>
