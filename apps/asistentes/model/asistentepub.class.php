<?php
namespace asistentes\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_asistentes_de_paso
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
/**
 * Classe que implementa l'entitat d_asistentes_de_paso
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class AsistentePub Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de AsistentePub
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de AsistentePub
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_activ de AsistentePub
	 *
	 * @var integer
	 */
	 protected $iid_activ;
	/**
	 * Id_nom de AsistentePub
	 *
	 * @var integer
	 */
	 protected $iid_nom;
	/**
	 * Propio de AsistentePub
	 *
	 * @var boolean
	 */
	 protected $bpropio;
	/**
	 * Est_ok de AsistentePub
	 *
	 * @var boolean
	 */
	 protected $best_ok;
	/**
	 * Cfi de AsistentePub
	 *
	 * @var boolean
	 */
	 protected $bcfi;
	/**
	 * Cfi_con de AsistentePub
	 *
	 * @var integer
	 */
	 protected $icfi_con;
	/**
	 * Falta de AsistentePub
	 *
	 * @var boolean
	 */
	 protected $bfalta;
	/**
	 * Encargo de AsistentePub
	 *
	 * @var string
	 */
	 protected $sencargo;
	/**
	 * Cama de AsistentePub
	 *
	 * @var string
	 */
	 protected $scama;
	/**
	 * Observ de AsistentePub
	 *
	 * @var string
	 */
	 protected $sobserv;
	/**
	 * Plaza de AsistentePub
	 *
	 * @var integer
	 */
	 protected $iplaza;
	/**
	 * Propietario de AsistentePub
	 *
	 * @var texet
	 */
	 protected $spropietario;
	/**
	 * id_tabla de AsistentePub
	 *
	 * @var string
	 */
	 protected $sid_tabla;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_activ,iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_asistentes_de_paso');
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
		$aDades['propio'] = $this->bpropio;
		$aDades['est_ok'] = $this->best_ok;
		$aDades['cfi'] = $this->bcfi;
		$aDades['cfi_con'] = $this->icfi_con;
		$aDades['falta'] = $this->bfalta;
		$aDades['encargo'] = $this->sencargo;
		$aDades['cama'] = $this->scama;
		$aDades['observ'] = $this->sobserv;
		$aDades['plaza'] = $this->iplaza;
		$aDades['propietario'] = $this->spropietario;
		//$aDades['id_tabla'] = $this->sid_tabla;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['propio']) || ($aDades['propio'] === 'off') || ($aDades['propio'] === false) || ($aDades['propio'] === 'f')) { $aDades['propio']='f'; } else { $aDades['propio']='t'; }
		if (empty($aDades['est_ok']) || ($aDades['est_ok'] === 'off') || ($aDades['est_ok'] === false) || ($aDades['est_ok'] === 'f')) { $aDades['est_ok']='f'; } else { $aDades['est_ok']='t'; }
		if (empty($aDades['cfi']) || ($aDades['cfi'] === 'off') || ($aDades['cfi'] === false) || ($aDades['cfi'] === 'f')) { $aDades['cfi']='f'; } else { $aDades['cfi']='t'; }
		if (empty($aDades['falta']) || ($aDades['falta'] === 'off') || ($aDades['falta'] === false) || ($aDades['falta'] === 'f')) { $aDades['falta']='f'; } else { $aDades['falta']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					propio                   = :propio,
					est_ok                   = :est_ok,
					cfi                      = :cfi,
					cfi_con                  = :cfi_con,
					falta                    = :falta,
					encargo                  = :encargo,
					cama                     = :cama,
					observ                   = :observ,
					plaza                    = :plaza,
					propietario              = :propietario";
					//id_tabla                 = :id_tabla";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = get_class($this).'.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = get_class($this).'.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_activ, $this->iid_nom);
			$campos="(id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,cama,observ,plaza,propietario)";
			$valores="(:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:cama,:observ,:plaza,:propietario)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = get_class($this).'.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = get_class($this).'.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
		if (isset($this->iid_activ) && isset($this->iid_nom)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = get_class($this).'.carregar';
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_nom='$this->iid_nom'")) === false) {
			$sClauError = get_class($this).'.eliminar';
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
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('propio',$aDades)) $this->setPropio($aDades['propio']);
		if (array_key_exists('est_ok',$aDades)) $this->setEst_ok($aDades['est_ok']);
		if (array_key_exists('cfi',$aDades)) $this->setCfi($aDades['cfi']);
		if (array_key_exists('cfi_con',$aDades)) $this->setCfi_con($aDades['cfi_con']);
		if (array_key_exists('falta',$aDades)) $this->setFalta($aDades['falta']);
		if (array_key_exists('encargo',$aDades)) $this->setEncargo($aDades['encargo']);
		if (array_key_exists('cama',$aDades)) $this->setCama($aDades['cama']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('plaza',$aDades)) $this->setPlaza($aDades['plaza']);
		if (array_key_exists('propietario',$aDades)) $this->setPropietario($aDades['propietario']);
		if (array_key_exists('id_tabla',$aDades)) $this->setId_tabla($aDades['id_tabla']);
	}
	/**
	 * retorna el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function getAllAtributes() {
		if (empty($aDades)) {
			$aDades = array();
			$aDades['id_activ'] = $this->getId_activ();
			$aDades['id_nom'] = $this->getId_nom();
			$aDades['propio'] = $this->getPropio();
			$aDades['est_ok'] = $this->getEst_ok();
			$aDades['cfi'] = $this->getCfi();
			$aDades['cfi_con'] = $this->getCfi_con();
			$aDades['falta'] = $this->getFalta();
			$aDades['encargo'] = $this->getEncargo();
			$aDades['cama'] = $this->getCama();
			$aDades['observ'] = $this->getObserv();
			$aDades['plaza'] = $this->getPlaza();
			$aDades['propietario'] = $this->getPropietario();
			$aDades['id_tabla'] = $this->getId_tabla();
		} else {
			return $aDades;
		}
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de AsistentePub en un array
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
	 * Recupera las claus primàries de AsistentePub en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_activ' => $this->iid_activ,'id_nom' => $this->iid_nom);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de AsistentePub en un array
	 *
	 * @return array aPrimary_key
	 */
	function setPrimary_key($a_id='') {
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
	}

	/**
	 * Recupera l'atribut iid_activ de AsistentePub
	 *
	 * @return integer iid_activ
	 */
	function getId_activ() {
		if (!isset($this->iid_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_activ de AsistentePub
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_nom de AsistentePub
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
	 * estableix el valor de l'atribut iid_nom de AsistentePub
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut bpropio de AsistentePub
	 *
	 * @return boolean bpropio
	 */
	function getPropio() {
		if (!isset($this->bpropio)) {
			$this->DBCarregar();
		}
		return $this->bpropio;
	}
	/**
	 * estableix el valor de l'atribut bpropio de AsistentePub
	 *
	 * @param boolean bpropio='f' optional
	 */
	function setPropio($bpropio='f') {
		$this->bpropio = $bpropio;
	}
	/**
	 * Recupera l'atribut best_ok de AsistentePub
	 *
	 * @return boolean best_ok
	 */
	function getEst_ok() {
		if (!isset($this->best_ok)) {
			$this->DBCarregar();
		}
		return $this->best_ok;
	}
	/**
	 * estableix el valor de l'atribut best_ok de AsistentePub
	 *
	 * @param boolean best_ok='f' optional
	 */
	function setEst_ok($best_ok='f') {
		$this->best_ok = $best_ok;
	}
	/**
	 * Recupera l'atribut bcfi de AsistentePub
	 *
	 * @return boolean bcfi
	 */
	function getCfi() {
		if (!isset($this->bcfi)) {
			$this->DBCarregar();
		}
		return $this->bcfi;
	}
	/**
	 * estableix el valor de l'atribut bcfi de AsistentePub
	 *
	 * @param boolean bcfi='f' optional
	 */
	function setCfi($bcfi='f') {
		$this->bcfi = $bcfi;
	}
	/**
	 * Recupera l'atribut icfi_con de AsistentePub
	 *
	 * @return integer icfi_con
	 */
	function getCfi_con() {
		if (!isset($this->icfi_con)) {
			$this->DBCarregar();
		}
		return $this->icfi_con;
	}
	/**
	 * estableix el valor de l'atribut icfi_con de AsistentePub
	 *
	 * @param integer icfi_con='' optional
	 */
	function setCfi_con($icfi_con='') {
		$this->icfi_con = $icfi_con;
	}
	/**
	 * Recupera l'atribut bfalta de AsistentePub
	 *
	 * @return boolean bfalta
	 */
	function getFalta() {
		if (!isset($this->bfalta)) {
			$this->DBCarregar();
		}
		return $this->bfalta;
	}
	/**
	 * estableix el valor de l'atribut bfalta de AsistentePub
	 *
	 * @param boolean bfalta='f' optional
	 */
	function setFalta($bfalta='f') {
		$this->bfalta = $bfalta;
	}
	/**
	 * Recupera l'atribut sencargo de AsistentePub
	 *
	 * @return string sencargo
	 */
	function getEncargo() {
		if (!isset($this->sencargo)) {
			$this->DBCarregar();
		}
		return $this->sencargo;
	}
	/**
	 * estableix el valor de l'atribut sencargo de AsistentePub
	 *
	 * @param string sencargo='' optional
	 */
	function setEncargo($sencargo='') {
		$this->sencargo = $sencargo;
	}
	/**
	 * Recupera l'atribut scama de AsistentePub
	 *
	 * @return string scama
	 */
	function getCama() {
		if (!isset($this->scama)) {
			$this->DBCarregar();
		}
		return $this->scama;
	}
	/**
	 * estableix el valor de l'atribut scama de AsistentePub
	 *
	 * @param string scama='' optional
	 */
	function setCama($scama='') {
		$this->scama = $scama;
	}
	/**
	 * Recupera l'atribut sobserv de AsistentePub
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
	 * estableix el valor de l'atribut sobserv de AsistentePub
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/**
	 * Recupera l'atribut iplaza de AsistentePub
	 *
	 * @return integer iplaza
	 */
	function getPlaza() {
		if (!isset($this->iplaza)) {
			$this->DBCarregar();
		}
		return $this->iplaza;
	}
	/**
	 * estableix el valor de l'atribut iplaza de AsistentePub
	 *
	 * @param integer iplaza='' optional
	 */
	function setPlaza($iplaza='') {
		$this->iplaza = $iplaza;
	}
	/**
	 * Recupera l'atribut spropietario de AsistentePub
	 *
	 * @return integer spropietario
	 */
	function getPropietario() {
		if (!isset($this->spropietario)) {
			$this->DBCarregar();
		}
		return $this->spropietario;
	}
	/**
	 * estableix el valor de l'atribut spropietario de AsistentePub
	 *
	 * @param integer spropietario='' optional
	 */
	function setPropietario($spropietario='') {
		$this->spropietario = $spropietario;
	}
	/**
	 * Recupera l'atribut sid_tabla de AsistentePub
	 *
	 * @return string sid_tabla
	 */
	function getId_tabla() {
		if (!isset($this->sid_tabla)) {
			$this->DBCarregar();
		}
		return $this->sid_tabla;
	}
	/**
	 * estableix el valor de l'atribut sid_tabla de AsistentePub
	 *
	 * @param string sid_tabla='' optional
	 */
	function setId_tabla($sid_tabla='') {
		$this->sid_tabla = $sid_tabla;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oAsistentePubSet = new core\Set();

		$oAsistentePubSet->add($this->getDatosPropio());
		$oAsistentePubSet->add($this->getDatosEst_ok());
		$oAsistentePubSet->add($this->getDatosCfi());
		$oAsistentePubSet->add($this->getDatosCfi_con());
		$oAsistentePubSet->add($this->getDatosFalta());
		$oAsistentePubSet->add($this->getDatosEncargo());
		$oAsistentePubSet->add($this->getDatosCama());
		$oAsistentePubSet->add($this->getDatosObserv());
		return $oAsistentePubSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut bpropio de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPropio() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'propio'));
		$oDatosCampo->setEtiqueta(_("propio"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut best_ok de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosEst_ok() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'est_ok'));
		$oDatosCampo->setEtiqueta(_("est_ok"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bcfi de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCfi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cfi'));
		$oDatosCampo->setEtiqueta(_("cfi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut icfi_con de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCfi_con() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cfi_con'));
		$oDatosCampo->setEtiqueta(_("cfi_con"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bfalta de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosFalta() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'falta'));
		$oDatosCampo->setEtiqueta(_("falta"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sencargo de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosEncargo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'encargo'));
		$oDatosCampo->setEtiqueta(_("encargo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scama de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCama() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cama'));
		$oDatosCampo->setEtiqueta(_("cama"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iplaza de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPlaza() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'plaza'));
		$oDatosCampo->setEtiqueta(_("plaza"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spropietario de AsistentePub
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPropietario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'propietario'));
		$oDatosCampo->setEtiqueta(_("propietario"));
		return $oDatosCampo;
	}
}
?>
