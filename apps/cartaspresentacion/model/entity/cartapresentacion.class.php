<?php
namespace cartaspresentacion\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula du_presentacion_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 8/4/2019
 */
/**
 * Classe que implementa l'entitat du_presentacion_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 8/4/2019
 */
class CartaPresentacion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de CartaPresentacion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de CartaPresentacion
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_direccion de CartaPresentacion
	 *
	 * @var integer
	 */
	 private $iid_direccion;
	/**
	 * Id_ubi de CartaPresentacion
	 *
	 * @var integer
	 */
	 private $iid_ubi;
	/**
	 * Pres_nom de CartaPresentacion
	 *
	 * @var string
	 */
	 private $spres_nom;
	/**
	 * Pres_telf de CartaPresentacion
	 *
	 * @var string
	 */
	 private $spres_telf;
	/**
	 * Pres_mail de CartaPresentacion
	 *
	 * @var string
	 */
	 private $spres_mail;
	/**
	 * Zona de CartaPresentacion
	 *
	 * @var string
	 */
	 private $szona;
	/**
	 * Observ de CartaPresentacion
	 *
	 * @var string
	 */
	 private $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de CartaPresentacion
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de CartaPresentacion
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
	 * @param integer|array iid_direccion,iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_direccion') && $val_id !== '') $this->iid_direccion = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('du_presentacion');
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
		$aDades['pres_nom'] = $this->spres_nom;
		$aDades['pres_telf'] = $this->spres_telf;
		$aDades['pres_mail'] = $this->spres_mail;
		$aDades['zona'] = $this->szona;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					pres_nom                 = :pres_nom,
					pres_telf                = :pres_telf,
					pres_mail                = :pres_mail,
					zona                     = :zona,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_direccion='$this->iid_direccion' AND id_ubi='$this->iid_ubi'")) === FALSE) {
				$sClauError = 'CartaPresentacion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CartaPresentacion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_direccion, $this->iid_ubi);
			$campos="(id_direccion,id_ubi,pres_nom,pres_telf,pres_mail,zona,observ)";
			$valores="(:id_direccion,:id_ubi,:pres_nom,:pres_telf,:pres_mail,:zona,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'CartaPresentacion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CartaPresentacion.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
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
		if (isset($this->iid_direccion) && isset($this->iid_ubi)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_direccion='$this->iid_direccion' AND id_ubi='$this->iid_ubi'")) === FALSE) {
				$sClauError = 'CartaPresentacion.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_direccion='$this->iid_direccion' AND id_ubi='$this->iid_ubi'")) === FALSE) {
			$sClauError = 'CartaPresentacion.eliminar';
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
		if (array_key_exists('id_direccion',$aDades)) $this->setId_direccion($aDades['id_direccion']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('pres_nom',$aDades)) $this->setPres_nom($aDades['pres_nom']);
		if (array_key_exists('pres_telf',$aDades)) $this->setPres_telf($aDades['pres_telf']);
		if (array_key_exists('pres_mail',$aDades)) $this->setPres_mail($aDades['pres_mail']);
		if (array_key_exists('zona',$aDades)) $this->setZona($aDades['zona']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_direccion('');
		$this->setId_ubi('');
		$this->setPres_nom('');
		$this->setPres_telf('');
		$this->setPres_mail('');
		$this->setZona('');
		$this->setObserv('');
	}



	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de CartaPresentacion en un array
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
	 * Recupera las claus primàries de CartaPresentacion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_direccion' => $this->iid_direccion,'id_ubi' => $this->iid_ubi);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_direccion de CartaPresentacion
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
	 * estableix el valor de l'atribut iid_direccion de CartaPresentacion
	 *
	 * @param integer iid_direccion
	 */
	function setId_direccion($iid_direccion) {
		$this->iid_direccion = $iid_direccion;
	}
	/**
	 * Recupera l'atribut iid_ubi de CartaPresentacion
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
	 * estableix el valor de l'atribut iid_ubi de CartaPresentacion
	 *
	 * @param integer iid_ubi
	 */
	function setId_ubi($iid_ubi) {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut spres_nom de CartaPresentacion
	 *
	 * @return string spres_nom
	 */
	function getPres_nom() {
		if (!isset($this->spres_nom)) {
			$this->DBCarregar();
		}
		return $this->spres_nom;
	}
	/**
	 * estableix el valor de l'atribut spres_nom de CartaPresentacion
	 *
	 * @param string spres_nom='' optional
	 */
	function setPres_nom($spres_nom='') {
		$this->spres_nom = $spres_nom;
	}
	/**
	 * Recupera l'atribut spres_telf de CartaPresentacion
	 *
	 * @return string spres_telf
	 */
	function getPres_telf() {
		if (!isset($this->spres_telf)) {
			$this->DBCarregar();
		}
		return $this->spres_telf;
	}
	/**
	 * estableix el valor de l'atribut spres_telf de CartaPresentacion
	 *
	 * @param string spres_telf='' optional
	 */
	function setPres_telf($spres_telf='') {
		$this->spres_telf = $spres_telf;
	}
	/**
	 * Recupera l'atribut spres_mail de CartaPresentacion
	 *
	 * @return string spres_mail
	 */
	function getPres_mail() {
		if (!isset($this->spres_mail)) {
			$this->DBCarregar();
		}
		return $this->spres_mail;
	}
	/**
	 * estableix el valor de l'atribut spres_mail de CartaPresentacion
	 *
	 * @param string spres_mail='' optional
	 */
	function setPres_mail($spres_mail='') {
		$this->spres_mail = $spres_mail;
	}
	/**
	 * Recupera l'atribut szona de CartaPresentacion
	 *
	 * @return string szona
	 */
	function getZona() {
		if (!isset($this->szona)) {
			$this->DBCarregar();
		}
		return $this->szona;
	}
	/**
	 * estableix el valor de l'atribut szona de CartaPresentacion
	 *
	 * @param string szona='' optional
	 */
	function setZona($szona='') {
		$this->szona = $szona;
	}
	/**
	 * Recupera l'atribut sobserv de CartaPresentacion
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv)) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de CartaPresentacion
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
		$oCartaPresentacionSet = new core\Set();

		$oCartaPresentacionSet->add($this->getDatosPres_nom());
		$oCartaPresentacionSet->add($this->getDatosPres_telf());
		$oCartaPresentacionSet->add($this->getDatosPres_mail());
		$oCartaPresentacionSet->add($this->getDatosZona());
		$oCartaPresentacionSet->add($this->getDatosObserv());
		return $oCartaPresentacionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut spres_nom de CartaPresentacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPres_nom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pres_nom'));
		$oDatosCampo->setEtiqueta(_("pres_nom"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spres_telf de CartaPresentacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPres_telf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pres_telf'));
		$oDatosCampo->setEtiqueta(_("pres_telf"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spres_mail de CartaPresentacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPres_mail() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pres_mail'));
		$oDatosCampo->setEtiqueta(_("pres_mail"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut szona de CartaPresentacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosZona() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'zona'));
		$oDatosCampo->setEtiqueta(_("zona"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de CartaPresentacion
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
