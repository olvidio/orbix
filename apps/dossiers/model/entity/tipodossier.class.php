<?php
namespace dossiers\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_tipos_dossiers
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 22/05/2014
 */
/**
 * Classe que implementa l'entitat d_tipos_dossiers
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 22/05/2014
 */
class TipoDossier Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoDossier
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoDossier
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_tipo_dossier de TipoDossier
	 *
	 * @var integer
	 */
	 private $iid_tipo_dossier;
	/**
	 * Descripcion de TipoDossier
	 *
	 * @var string
	 */
	 private $sdescripcion;
	/**
	 * Tabla_from de TipoDossier
	 *
	 * @var string
	 */
	 private $stabla_from;
	/**
	 * Tabla_to de TipoDossier
	 *
	 * @var string
	 */
	 private $stabla_to;
	/**
	 * Campo_to de TipoDossier
	 *
	 * @var string
	 */
	 private $scampo_to;
	/**
	 * Id_tipo_dossier_rel de TipoDossier
	 *
	 * @var integer
	 */
	 private $iid_tipo_dossier_rel;
	/**
	 * Permiso_lectura de TipoDossier
	 *
	 * @var integer
	 */
	 private $ipermiso_lectura;
	/**
	 * Permiso_escritura de TipoDossier
	 *
	 * @var integer
	 */
	 private $ipermiso_escritura;
	/**
	 * Depende_modificar de TipoDossier
	 *
	 * @var boolean
	 */
	 private $bdepende_modificar;
	/**
	 * App de TipoDossier
	 *
	 * @var string
	 */
	 private $sapp;
	/**
	 * Class de TipoDossier
	 *
	 * @var string
	 */
	 private $sclass;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de TipoDossier
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de TipoDossier
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
	 * @param integer|array iid_tipo_dossier
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_tipo_dossier') && $val_id !== '') $this->iid_tipo_dossier = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_tipo_dossier = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_tipo_dossier' => $this->iid_tipo_dossier);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_tipos_dossiers');
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
		$aDades['descripcion'] = $this->sdescripcion;
		$aDades['tabla_from'] = $this->stabla_from;
		$aDades['tabla_to'] = $this->stabla_to;
		$aDades['campo_to'] = $this->scampo_to;
		$aDades['id_tipo_dossier_rel'] = $this->iid_tipo_dossier_rel;
		$aDades['permiso_lectura'] = $this->ipermiso_lectura;
		$aDades['permiso_escritura'] = $this->ipermiso_escritura;
		$aDades['depende_modificar'] = $this->bdepende_modificar;
		$aDades['app'] = $this->sapp;
		$aDades['class'] = $this->sclass;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( filter_var( $aDades['depende_modificar'], FILTER_VALIDATE_BOOLEAN)) { $aDades['depende_modificar']='t'; } else { $aDades['depende_modificar']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					descripcion              = :descripcion,
					tabla_from               = :tabla_from,
					tabla_to                 = :tabla_to,
					campo_to                 = :campo_to,
					id_tipo_dossier_rel      = :id_tipo_dossier_rel,
					permiso_lectura          = :permiso_lectura,
					permiso_escritura        = :permiso_escritura,
					depende_modificar        = :depende_modificar,
					app                      = :app,
					class                    = :class";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
				$sClauError = 'TipoDossier.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoDossier.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_tipo_dossier);
			$campos="(id_tipo_dossier,descripcion,tabla_from,tabla_to,campo_to,id_tipo_dossier_rel,permiso_lectura,permiso_escritura,depende_modificar,app,class)";
			$valores="(:id_tipo_dossier,:descripcion,:tabla_from,:tabla_to,:campo_to,:id_tipo_dossier_rel,:permiso_lectura,:permiso_escritura,:depende_modificar,:app,:class)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TipoDossier.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoDossier.insertar.execute';
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
		if (isset($this->iid_tipo_dossier)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
				$sClauError = 'TipoDossier.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
			$sClauError = 'TipoDossier.eliminar';
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
		if (array_key_exists('id_tipo_dossier',$aDades)) $this->setId_tipo_dossier($aDades['id_tipo_dossier']);
		if (array_key_exists('descripcion',$aDades)) $this->setDescripcion($aDades['descripcion']);
		if (array_key_exists('tabla_from',$aDades)) $this->setTabla_from($aDades['tabla_from']);
		if (array_key_exists('tabla_to',$aDades)) $this->setTabla_to($aDades['tabla_to']);
		if (array_key_exists('campo_to',$aDades)) $this->setCampo_to($aDades['campo_to']);
		if (array_key_exists('id_tipo_dossier_rel',$aDades)) $this->setId_tipo_dossier_rel($aDades['id_tipo_dossier_rel']);
		if (array_key_exists('permiso_lectura',$aDades)) $this->setPermiso_lectura($aDades['permiso_lectura']);
		if (array_key_exists('permiso_escritura',$aDades)) $this->setPermiso_escritura($aDades['permiso_escritura']);
		if (array_key_exists('depende_modificar',$aDades)) $this->setDepende_modificar($aDades['depende_modificar']);
		if (array_key_exists('app',$aDades)) $this->setApp($aDades['app']);
		if (array_key_exists('class',$aDades)) $this->setClass($aDades['class']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de TipoDossier en un array
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
	 * Recupera las claus primàries de TipoDossier en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_tipo_dossier' => $this->iid_tipo_dossier);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_tipo_dossier de TipoDossier
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
	 * estableix el valor de l'atribut iid_tipo_dossier de TipoDossier
	 *
	 * @param integer iid_tipo_dossier
	 */
	function setId_tipo_dossier($iid_tipo_dossier) {
		$this->iid_tipo_dossier = $iid_tipo_dossier;
	}
	/**
	 * Recupera l'atribut sdescripcion de TipoDossier
	 *
	 * @return string sdescripcion
	 */
	function getDescripcion() {
		if (!isset($this->sdescripcion)) {
			$this->DBCarregar();
		}
		return $this->sdescripcion;
	}
	/**
	 * estableix el valor de l'atribut sdescripcion de TipoDossier
	 *
	 * @param string sdescripcion='' optional
	 */
	function setDescripcion($sdescripcion='') {
		$this->sdescripcion = $sdescripcion;
	}
	/**
	 * Recupera l'atribut stabla_from de TipoDossier
	 *
	 * @return string stabla_from
	 */
	function getTabla_from() {
		if (!isset($this->stabla_from)) {
			$this->DBCarregar();
		}
		return $this->stabla_from;
	}
	/**
	 * estableix el valor de l'atribut stabla_from de TipoDossier
	 *
	 * @param string stabla_from='' optional
	 */
	function setTabla_from($stabla_from='') {
		$this->stabla_from = $stabla_from;
	}
	/**
	 * Recupera l'atribut stabla_to de TipoDossier
	 *
	 * @return string stabla_to
	 */
	function getTabla_to() {
		if (!isset($this->stabla_to)) {
			$this->DBCarregar();
		}
		return $this->stabla_to;
	}
	/**
	 * estableix el valor de l'atribut stabla_to de TipoDossier
	 *
	 * @param string stabla_to='' optional
	 */
	function setTabla_to($stabla_to='') {
		$this->stabla_to = $stabla_to;
	}
	/**
	 * Recupera l'atribut scampo_to de TipoDossier
	 *
	 * @return string scampo_to
	 */
	function getCampo_to() {
		if (!isset($this->scampo_to)) {
			$this->DBCarregar();
		}
		return $this->scampo_to;
	}
	/**
	 * estableix el valor de l'atribut scampo_to de TipoDossier
	 *
	 * @param string scampo_to='' optional
	 */
	function setCampo_to($scampo_to='') {
		$this->scampo_to = $scampo_to;
	}
	/**
	 * Recupera l'atribut iid_tipo_dossier_rel de TipoDossier
	 *
	 * @return integer iid_tipo_dossier_rel
	 */
	function getId_tipo_dossier_rel() {
		if (!isset($this->iid_tipo_dossier_rel)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_dossier_rel;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_dossier_rel de TipoDossier
	 *
	 * @param integer iid_tipo_dossier_rel='' optional
	 */
	function setId_tipo_dossier_rel($iid_tipo_dossier_rel='') {
		$this->iid_tipo_dossier_rel = $iid_tipo_dossier_rel;
	}
	/**
	 * Recupera l'atribut ipermiso_lectura de TipoDossier
	 *
	 * @return integer ipermiso_lectura
	 */
	function getPermiso_lectura() {
		if (!isset($this->ipermiso_lectura)) {
			$this->DBCarregar();
		}
		return $this->ipermiso_lectura;
	}
	/**
	 * estableix el valor de l'atribut ipermiso_lectura de TipoDossier
	 *
	 * @param integer ipermiso_lectura='' optional
	 */
	function setPermiso_lectura($ipermiso_lectura='') {
		$this->ipermiso_lectura = $ipermiso_lectura;
	}
	/**
	 * Recupera l'atribut ipermiso_escritura de TipoDossier
	 *
	 * @return integer ipermiso_escritura
	 */
	function getPermiso_escritura() {
		if (!isset($this->ipermiso_escritura)) {
			$this->DBCarregar();
		}
		return $this->ipermiso_escritura;
	}
	/**
	 * estableix el valor de l'atribut ipermiso_escritura de TipoDossier
	 *
	 * @param integer ipermiso_escritura='' optional
	 */
	function setPermiso_escritura($ipermiso_escritura='') {
		$this->ipermiso_escritura = $ipermiso_escritura;
	}
	/**
	 * Recupera l'atribut bdepende_modificar de TipoDossier
	 *
	 * @return boolean bdepende_modificar
	 */
	function getDepende_modificar() {
		if (!isset($this->bdepende_modificar)) {
			$this->DBCarregar();
		}
		return $this->bdepende_modificar;
	}
	/**
	 * estableix el valor de l'atribut bdepende_modificar de TipoDossier
	 *
	 * @param boolean bdepende_modificar='f' optional
	 */
	function setDepende_modificar($bdepende_modificar='f') {
		$this->bdepende_modificar = $bdepende_modificar;
	}
	/**
	 * Recupera l'atribut sapp de TipoDossier
	 *
	 * @return string sapp
	 */
	function getApp() {
		if (!isset($this->sapp)) {
			$this->DBCarregar();
		}
		return $this->sapp;
	}
	/**
	 * estableix el valor de l'atribut sapp de TipoDossier
	 *
	 * @param string sapp='' optional
	 */
	function setApp($sapp='') {
		$this->sapp = $sapp;
	}
	/**
	 * Recupera l'atribut sclass de TipoDossier
	 *
	 * @return string sclass
	 */
	function getClass() {
		if (!isset($this->sclass)) {
			$this->DBCarregar();
		}
		return $this->sclass;
	}
	/**
	 * estableix el valor de l'atribut sclass de TipoDossier
	 *
	 * @param string sclass='' optional
	 */
	function setClass($sclass='') {
		$this->sclass = $sclass;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTipoDossierSet = new core\Set();

		$oTipoDossierSet->add($this->getDatosDescripcion());
		$oTipoDossierSet->add($this->getDatosTabla_from());
		$oTipoDossierSet->add($this->getDatosTabla_to());
		$oTipoDossierSet->add($this->getDatosCampo_to());
		$oTipoDossierSet->add($this->getDatosId_tipo_dossier_rel());
		$oTipoDossierSet->add($this->getDatosPermiso_lectura());
		$oTipoDossierSet->add($this->getDatosPermiso_escritura());
		$oTipoDossierSet->add($this->getDatosDepende_modificar());
		$oTipoDossierSet->add($this->getDatosApp());
		$oTipoDossierSet->add($this->getDatosClass());
		return $oTipoDossierSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut sdescripcion de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDescripcion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'descripcion'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stabla_from de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTabla_from() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tabla_from'));
		$oDatosCampo->setEtiqueta(_("tabla_from"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stabla_to de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTabla_to() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tabla_to'));
		$oDatosCampo->setEtiqueta(_("tabla_to"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scampo_to de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCampo_to() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'campo_to'));
		$oDatosCampo->setEtiqueta(_("campo_to"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tipo_dossier_rel de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_dossier_rel() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_dossier_rel'));
		$oDatosCampo->setEtiqueta(_("id_tipo_dossier_rel"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ipermiso_lectura de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPermiso_lectura() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'permiso_lectura'));
		$oDatosCampo->setEtiqueta(_("permiso de lectura"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ipermiso_escritura de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPermiso_escritura() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'permiso_escritura'));
		$oDatosCampo->setEtiqueta(_("permiso de escritura"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bdepende_modificar de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDepende_modificar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'depende_modificar'));
		$oDatosCampo->setEtiqueta(_("depende modificar"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sapp de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosApp() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'app'));
		$oDatosCampo->setEtiqueta(_("app"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sclass de TipoDossier
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosClass() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'class'));
		$oDatosCampo->setEtiqueta(_("class"));
		return $oDatosCampo;
	}
}
?>
