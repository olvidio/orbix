<?php
namespace ubis\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula du_tarifas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2018
 */
/**
 * Classe que implementa l'entitat du_tarifas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2018
 */
class Tarifa Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Tarifa
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Tarifa
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de Tarifa
	 *
	 * @var integer
	 */
	 private $iid_schema;
	/**
	 * Id_item de Tarifa
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_ubi de Tarifa
	 *
	 * @var integer
	 */
	 private $iid_ubi;
	/**
	 * Id_tarifa de Tarifa
	 *
	 * @var integer
	 */
	 private $iid_tarifa;
	/**
	 * Year de Tarifa
	 *
	 * @var integer
	 */
	 private $iyear;
	/**
	 * Cantidad de Tarifa
	 *
	 * @var float
	 */
	 private $icantidad;
	/**
	 * Observ de Tarifa
	 *
	 * @var string
	 */
	 private $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Tarifa
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Tarifa
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
	 * @param integer|array iid_ubi,iid_tarifa,iyear
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
		        $this->iid_item = (integer) $a_id; // evitem SQL injection fent cast a integer
		        $this->aPrimary_key = array('id_item' => $this->iid_item);
		    }
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('du_tarifas');
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
		$aDades['id_ubi'] = $this->iid_ubi;
		$aDades['id_tarifa'] = $this->iid_tarifa;
		$aDades['year'] = $this->iyear;
		$aDades['cantidad'] = $this->icantidad;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_ubi                   = :id_ubi,
					id_tarifa                = :id_tarifa,
					year                     = :year,
					cantidad                 = :cantidad,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'Tarifa.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Tarifa.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_ubi, $this->iid_tarifa, $this->iyear);
			$campos="(id_ubi,id_tarifa,year,cantidad,observ)";
			$valores="(:id_ubi,:id_tarifa,:year,:cantidad,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Tarifa.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Tarifa.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('du_tarifas_id_item_seq');
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
				$sClauError = 'Tarifa.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			// Para evitar posteriores cargas
			$this->bLoaded = TRUE;
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
			$sClauError = 'Tarifa.eliminar';
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
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('id_tarifa',$aDades)) $this->setId_tarifa($aDades['id_tarifa']);
		if (array_key_exists('year',$aDades)) $this->setYear($aDades['year']);
		if (array_key_exists('cantidad',$aDades)) $this->setCantidad($aDades['cantidad']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setId_ubi('');
		$this->setId_tarifa('');
		$this->setYear('');
		$this->setCantidad('');
		$this->setObserv('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Tarifa en un array
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
	 * Recupera las claus primàries de Tarifa en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_ubi' => $this->iid_ubi,'id_tarifa' => $this->iid_tarifa,'year' => $this->iyear);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de Tarifa en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'id_tarifa') && $val_id !== '') $this->iid_tarifa = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'year') && $val_id !== '') $this->iyear = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_schema de Tarifa
	 *
	 * @return integer iid_schema
	 */
	function getId_schema() {
		if (!isset($this->iid_schema) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_schema;
	}
	/**
	 * Recupera l'atribut iid_item de Tarifa
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de Tarifa
	 *
	 * @param integer iid_item='' optional
	 */
	function setId_item($iid_item='') {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_ubi de Tarifa
	 *
	 * @return integer iid_ubi
	 */
	function getId_ubi() {
		if (!isset($this->iid_ubi) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_ubi;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi de Tarifa
	 *
	 * @param integer iid_ubi
	 */
	function setId_ubi($iid_ubi) {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut iid_tarifa de Tarifa
	 *
	 * @return integer iid_tarifa
	 */
	function getId_tarifa() {
		if (!isset($this->iid_tarifa) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_tarifa;
	}
	/**
	 * estableix el valor de l'atribut iid_tarifa de Tarifa
	 *
	 * @param integer iid_tarifa
	 */
	function setId_tarifa($iid_tarifa) {
		$this->iid_tarifa = $iid_tarifa;
	}
	/**
	 * Recupera l'atribut iyear de Tarifa
	 *
	 * @return integer iyear
	 */
	function getYear() {
		if (!isset($this->iyear) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iyear;
	}
	/**
	 * estableix el valor de l'atribut iyear de Tarifa
	 *
	 * @param integer iyear
	 */
	function setYear($iyear) {
		$this->iyear = $iyear;
	}
	/**
	 * Recupera l'atribut icantidad de Tarifa
	 *
	 * @return float icantidad
	 */
	function getCantidad() {
		if (!isset($this->icantidad) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->icantidad;
	}
	/**
	 * estableix el valor de l'atribut icantidad de Tarifa
	 *
	 * @param float icantidad='' optional
	 */
	function setCantidad($icantidad='') {
		$this->icantidad = $icantidad;
	}
	/**
	 * Recupera l'atribut sobserv de Tarifa
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de Tarifa
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTarifaSet = new core\Set();
		$oTarifaSet->add($this->getDatosId_item());
		$oTarifaSet->add($this->getDatosCantidad());
		$oTarifaSet->add($this->getDatosObserv());
		return $oTarifaSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut iid_item de Tarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_item() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_item'));
		$oDatosCampo->setEtiqueta(_("id_item"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut icantidad de Tarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCantidad() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cantidad'));
		$oDatosCampo->setEtiqueta(_("cantidad"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de Tarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
}
