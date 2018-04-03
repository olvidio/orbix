<?php
namespace dossiers\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/11/2014
 */
/**
 * Classe que implementa l'entitat d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/11/2014
 */
class Dossier Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Dossier
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Dossier
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_schema de Dossier
	 *
	 * @var integer
	 */
	 private $iid_schema;
	/**
	 * Tabla de Dossier
	 *
	 * @var string
	 */
	 private $stabla;
	/**
	 * Id_pau de Dossier
	 *
	 * @var string
	 */
	 private $sid_pau;
	/**
	 * Id_tipo_dossier de Dossier
	 *
	 * @var integer
	 */
	 private $iid_tipo_dossier;
	/**
	 * F_ini de Dossier
	 *
	 * @var date
	 */
	 private $df_ini;
	/**
	 * F_camb_dossier de Dossier
	 *
	 * @var date
	 */
	 private $df_camb_dossier;
	/**
	 * Status_dossier de Dossier
	 *
	 * @var boolean
	 */
	 private $bstatus_dossier;
	/**
	 * F_status de Dossier
	 *
	 * @var date
	 */
	 private $df_status;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Dossier
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Dossier
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
	 * @param integer|array stabla,sid_pau,iid_tipo_dossier
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'tabla') && $val_id !== '') $this->stabla = (string)$val_id; // evitem SQL injection fent cast a string
				if (($nom_id == 'id_pau') && $val_id !== '') $this->sid_pau = (string)$val_id; // evitem SQL injection fent cast a string
				if (($nom_id == 'id_tipo_dossier') && $val_id !== '') $this->iid_tipo_dossier = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_dossiers_abiertos');
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
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_camb_dossier'] = $this->df_camb_dossier;
		$aDades['status_dossier'] = $this->bstatus_dossier;
		$aDades['f_status'] = $this->df_status;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['status_dossier']) || ($aDades['status_dossier'] === 'off') || ($aDades['status_dossier'] === false) || ($aDades['status_dossier'] === 'f')) { $aDades['status_dossier']='f'; } else { $aDades['status_dossier']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					f_ini                    = :f_ini,
					f_camb_dossier           = :f_camb_dossier,
					status_dossier           = :status_dossier,
					f_status                 = :f_status";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tabla='$this->stabla' AND id_pau='$this->sid_pau' AND id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
				$sClauError = 'Dossier.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Dossier.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->stabla, $this->sid_pau, $this->iid_tipo_dossier);
			$campos="(tabla,id_pau,id_tipo_dossier,f_ini,f_camb_dossier,status_dossier,f_status)";
			$valores="(:tabla,:id_pau,:id_tipo_dossier,:f_ini,:f_camb_dossier,:status_dossier,:f_status)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Dossier.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Dossier.insertar.execute';
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
		if (isset($this->stabla) && isset($this->sid_pau) && isset($this->iid_tipo_dossier)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE tabla='$this->stabla' AND id_pau='$this->sid_pau' AND id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
				$sClauError = 'Dossier.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE tabla='$this->stabla' AND id_pau='$this->sid_pau' AND id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
			$sClauError = 'Dossier.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (array_key_exists('tabla',$aDades)) $this->setTabla($aDades['tabla']);
		if (array_key_exists('id_pau',$aDades)) $this->setId_pau($aDades['id_pau']);
		if (array_key_exists('id_tipo_dossier',$aDades)) $this->setId_tipo_dossier($aDades['id_tipo_dossier']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini']);
		if (array_key_exists('f_camb_dossier',$aDades)) $this->setF_camb_dossier($aDades['f_camb_dossier']);
		if (array_key_exists('status_dossier',$aDades)) $this->setStatus_dossier($aDades['status_dossier']);
		if (array_key_exists('f_status',$aDades)) $this->setF_status($aDades['f_status']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	* Posa la data d'avuvi a f_status i status a true
	*
	* @return none
	*/
	function abrir() {
	$this->DBCarregar();
	$this->setF_status(date("d/m/Y"));
	$this->setStatus_dossier('t');
	$this->DBGuardar();
	}
	/**
	* Posa la data d'avuvi a f_status i status a false
	*
	* @return none
	*/
	function cerrar() {
	$this->DBCarregar();
	$this->setF_status(date("d/m/Y"));
	$this->setStatus_dossier('f');
	$this->DBGuardar();
	}

	/**
	 * Recupera tots els atributs de Dossier en un array
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
	 * Recupera las claus primàries de Dossier en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('tabla' => $this->stabla,'id_pau' => $this->sid_pau,'id_tipo_dossier' => $this->iid_tipo_dossier);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_schema de Dossier
	 *
	 * @return integer iid_schema
	 */
	function getId_schema() {
		if (!isset($this->iid_schema)) {
			$this->DBCarregar();
		}
		return $this->iid_schema;
	}
	/**
	 * estableix el valor de l'atribut iid_schema de Dossier
	 *
	 * @param integer iid_schema='' optional
	 */
	function setId_schema($iid_schema='') {
		$this->iid_schema = $iid_schema;
	}
	/**
	 * Recupera l'atribut stabla de Dossier
	 *
	 * @return string stabla
	 */
	function getTabla() {
		if (!isset($this->stabla)) {
			$this->DBCarregar();
		}
		return $this->stabla;
	}
	/**
	 * estableix el valor de l'atribut stabla de Dossier
	 *
	 * @param string stabla
	 */
	function setTabla($stabla) {
		$this->stabla = $stabla;
	}
	/**
	 * Recupera l'atribut sid_pau de Dossier
	 *
	 * @return string sid_pau
	 */
	function getId_pau() {
		if (!isset($this->sid_pau)) {
			$this->DBCarregar();
		}
		return $this->sid_pau;
	}
	/**
	 * estableix el valor de l'atribut sid_pau de Dossier
	 *
	 * @param string sid_pau
	 */
	function setId_pau($sid_pau) {
		$this->sid_pau = $sid_pau;
	}
	/**
	 * Recupera l'atribut iid_tipo_dossier de Dossier
	 *
	 * @return integer iid_tipo_dossier
	 */
	function getId_tipo_dossier() {
		if (!isset($this->iid_tipo_dossier)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_dossier;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_dossier de Dossier
	 *
	 * @param integer iid_tipo_dossier
	 */
	function setId_tipo_dossier($iid_tipo_dossier) {
		$this->iid_tipo_dossier = $iid_tipo_dossier;
	}
	/**
	 * Recupera l'atribut df_ini de Dossier
	 *
	 * @return date df_ini
	 */
	function getF_ini() {
		if (!isset($this->df_ini)) {
			$this->DBCarregar();
		}
		return $this->df_ini;
	}
	/**
	 * estableix el valor de l'atribut df_ini de Dossier
	 *
	 * @param date df_ini='' optional
	 */
	function setF_ini($df_ini='') {
		$this->df_ini = $df_ini;
	}
	/**
	 * Recupera l'atribut df_camb_dossier de Dossier
	 *
	 * @return date df_camb_dossier
	 */
	function getF_camb_dossier() {
		if (!isset($this->df_camb_dossier)) {
			$this->DBCarregar();
		}
		return $this->df_camb_dossier;
	}
	/**
	 * estableix el valor de l'atribut df_camb_dossier de Dossier
	 *
	 * @param date df_camb_dossier='' optional
	 */
	function setF_camb_dossier($df_camb_dossier='') {
		$this->df_camb_dossier = $df_camb_dossier;
	}
	/**
	 * Recupera l'atribut bstatus_dossier de Dossier
	 *
	 * @return boolean bstatus_dossier
	 */
	function getStatus_dossier() {
		if (!isset($this->bstatus_dossier)) {
			$this->DBCarregar();
		}
		return $this->bstatus_dossier;
	}
	/**
	 * estableix el valor de l'atribut bstatus_dossier de Dossier
	 *
	 * @param boolean bstatus_dossier='f' optional
	 */
	function setStatus_dossier($bstatus_dossier='f') {
		$this->bstatus_dossier = $bstatus_dossier;
	}
	/**
	 * Recupera l'atribut df_status de Dossier
	 *
	 * @return date df_status
	 */
	function getF_status() {
		if (!isset($this->df_status)) {
			$this->DBCarregar();
		}
		return $this->df_status;
	}
	/**
	 * estableix el valor de l'atribut df_status de Dossier
	 *
	 * @param date df_status='' optional
	 */
	function setF_status($df_status='') {
		$this->df_status = $df_status;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDossierSet = new core\Set();

		$oDossierSet->add($this->getDatosId_schema());
		$oDossierSet->add($this->getDatosF_ini());
		$oDossierSet->add($this->getDatosF_camb_dossier());
		$oDossierSet->add($this->getDatosStatus_dossier());
		$oDossierSet->add($this->getDatosF_status());
		return $oDossierSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema de Dossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_schema() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_schema'));
		$oDatosCampo->setEtiqueta(_("id_schema"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de Dossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_ini'));
		$oDatosCampo->setEtiqueta(_("f_ini"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_camb_dossier de Dossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_camb_dossier() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_camb_dossier'));
		$oDatosCampo->setEtiqueta(_("f_camb_dossier"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bstatus_dossier de Dossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosStatus_dossier() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'status_dossier'));
		$oDatosCampo->setEtiqueta(_("status_dossier"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_status de Dossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_status() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_status'));
		$oDatosCampo->setEtiqueta(_("f_status"));
		return $oDatosCampo;
	}
}
?>
