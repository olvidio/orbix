<?php
namespace casas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula du_grupos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 1/7/2019
 */
/**
 * Classe que implementa l'entitat du_grupos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 1/7/2019
 */
class GrupoCasa Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de GrupoCasa
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de GrupoCasa
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de GrupoCasa
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_ubi_padre de GrupoCasa
	 *
	 * @var integer
	 */
	 private $iid_ubi_padre;
	/**
	 * Id_ubi_hijo de GrupoCasa
	 *
	 * @var integer
	 */
	 private $iid_ubi_hijo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de GrupoCasa
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de GrupoCasa
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
		$oDbl = $GLOBALS['oDBC'];
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
		$this->setNomTabla('du_grupos_dl');
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
		$aDades['id_ubi_padre'] = $this->iid_ubi_padre;
		$aDades['id_ubi_hijo'] = $this->iid_ubi_hijo;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_ubi_padre             = :id_ubi_padre,
					id_ubi_hijo              = :id_ubi_hijo";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'GrupoCasa.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'GrupoCasa.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_ubi_padre,id_ubi_hijo)";
			$valores="(:id_ubi_padre,:id_ubi_hijo)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'GrupoCasa.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'GrupoCasa.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('du_grupos_dl_id_item_seq');
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
				$sClauError = 'GrupoCasa.carregar';
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
					$this->setAllAtributes($aDades);
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
			$sClauError = 'GrupoCasa.eliminar';
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
		if (array_key_exists('id_ubi_padre',$aDades)) $this->setId_ubi_padre($aDades['id_ubi_padre']);
		if (array_key_exists('id_ubi_hijo',$aDades)) $this->setId_ubi_hijo($aDades['id_ubi_hijo']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de GrupoCasa en un array
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
	 * Recupera las claus primàries de GrupoCasa en un array
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
	 * Recupera l'atribut iid_item de GrupoCasa
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
	 * estableix el valor de l'atribut iid_item de GrupoCasa
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_ubi_padre de GrupoCasa
	 *
	 * @return integer iid_ubi_padre
	 */
	function getId_ubi_padre() {
		if (!isset($this->iid_ubi_padre)) {
			$this->DBCarregar();
		}
		return $this->iid_ubi_padre;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi_padre de GrupoCasa
	 *
	 * @param integer iid_ubi_padre='' optional
	 */
	function setId_ubi_padre($iid_ubi_padre='') {
		$this->iid_ubi_padre = $iid_ubi_padre;
	}
	/**
	 * Recupera l'atribut iid_ubi_hijo de GrupoCasa
	 *
	 * @return integer iid_ubi_hijo
	 */
	function getId_ubi_hijo() {
		if (!isset($this->iid_ubi_hijo)) {
			$this->DBCarregar();
		}
		return $this->iid_ubi_hijo;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi_hijo de GrupoCasa
	 *
	 * @param integer iid_ubi_hijo='' optional
	 */
	function setId_ubi_hijo($iid_ubi_hijo='') {
		$this->iid_ubi_hijo = $iid_ubi_hijo;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oGrupoCasaSet = new core\Set();

		$oGrupoCasaSet->add($this->getDatosId_ubi_padre());
		$oGrupoCasaSet->add($this->getDatosId_ubi_hijo());
		return $oGrupoCasaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_ubi_padre de GrupoCasa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi_padre() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi_padre'));
		$oDatosCampo->setEtiqueta(_("id_ubi_padre"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_ubi_hijo de GrupoCasa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi_hijo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi_hijo'));
		$oDatosCampo->setEtiqueta(_("id_ubi_hijo"));
		return $oDatosCampo;
	}
}
