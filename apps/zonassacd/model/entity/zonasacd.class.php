<?php
namespace zonassacd\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula zonas_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
/**
 * Classe que implementa l'entitat zonas_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
class ZonaSacd Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ZonaSacd
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ZonaSacd
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de ZonaSacd
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_nom de ZonaSacd
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Id_zona de ZonaSacd
	 *
	 * @var integer
	 */
	 private $iid_zona;
	/**
	 * Propia de ZonaSacd
	 *
	 * @var boolean
	 */
	 private $bpropia;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ZonaSacd
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ZonaSacd
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
		$this->setNomTabla('zonas_sacd');
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
		$aDades['id_nom'] = $this->iid_nom;
		$aDades['id_zona'] = $this->iid_zona;
		$aDades['propia'] = $this->bpropia;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['propia'] = ($aDades['propia'] === 't')? 'true' : $aDades['propia'];
		if ( filter_var( $aDades['propia'], FILTER_VALIDATE_BOOLEAN)) { $aDades['propia']='t'; } else { $aDades['propia']='f'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_nom                   = :id_nom,
					id_zona                  = :id_zona,
					propia                   = :propia";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'ZonaSacd.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ZonaSacd.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_nom,id_zona,propia)";
			$valores="(:id_nom,:id_zona,:propia)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'ZonaSacd.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ZonaSacd.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('zonas_sacd_id_item_seq');
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
				$sClauError = 'ZonaSacd.carregar';
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
				default:					// En el caso de no existir esta fila, $aDades = FALSE:					if ($aDades === FALSE) {
						$this->setNullAllAtributes();					} else {						$this->setAllAtributes($aDades);					}			}
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
			$sClauError = 'ZonaSacd.eliminar';
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
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('id_zona',$aDades)) $this->setId_zona($aDades['id_zona']);
		if (array_key_exists('propia',$aDades)) $this->setPropia($aDades['propia']);
	}	/**	 * Estableix a empty el valor de tots els atributs	 *	 */	function setNullAllAtributes() {
		$this->setId_item('');
		$this->setId_nom('');
		$this->setId_zona('');
		$this->setPropia('');
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ZonaSacd en un array
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
	 * Recupera las claus primàries de ZonaSacd en un array
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
	 * Recupera l'atribut iid_item de ZonaSacd
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
	 * estableix el valor de l'atribut iid_item de ZonaSacd
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_nom de ZonaSacd
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		if (!isset($this->iid_nom)) {
			$this->DBCarregar();
		}
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de ZonaSacd
	 *
	 * @param integer iid_nom='' optional
	 */
	function setId_nom($iid_nom='') {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_zona de ZonaSacd
	 *
	 * @return integer iid_zona
	 */
	function getId_zona() {
		if (!isset($this->iid_zona)) {
			$this->DBCarregar();
		}
		return $this->iid_zona;
	}
	/**
	 * estableix el valor de l'atribut iid_zona de ZonaSacd
	 *
	 * @param integer iid_zona='' optional
	 */
	function setId_zona($iid_zona='') {
		$this->iid_zona = $iid_zona;
	}
	/**
	 * Recupera l'atribut bpropia de ZonaSacd
	 *
	 * @return boolean bpropia
	 */
	function getPropia() {
		if (!isset($this->bpropia)) {
			$this->DBCarregar();
		}
		return $this->bpropia;
	}
	/**
	 * estableix el valor de l'atribut bpropia de ZonaSacd
	 *
	 * @param boolean bpropia='f' optional
	 */
	function setPropia($bpropia='f') {
		$this->bpropia = $bpropia;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oZonaSacdSet = new core\Set();

		$oZonaSacdSet->add($this->getDatosId_nom());
		$oZonaSacdSet->add($this->getDatosId_zona());
		$oZonaSacdSet->add($this->getDatosPropia());
		return $oZonaSacdSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_nom de ZonaSacd
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_nom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_nom'));
		$oDatosCampo->setEtiqueta(_("id_nom"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_zona de ZonaSacd
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_zona() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_zona'));
		$oDatosCampo->setEtiqueta(_("id_zona"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpropia de ZonaSacd
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPropia() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'propia'));
		$oDatosCampo->setEtiqueta(_("propia"));
		return $oDatosCampo;
	}
}
