<?php
namespace ubis\model;
use core;
/**
 * Classe que implementa l'entitat u_centros
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */

class Centro Extends UbiGlobal {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Tipo_ctr de Centro
	 *
	 * @var string
	 */
	 protected $stipo_ctr;
	/**
	 * Tipo_labor de Centro
	 *
	 * @var integer
	 */
	 protected $itipo_labor;
	/**
	 * Cdc de Centro
	 *
	 * @var boolean
	 */
	 protected $bcdc;
	/**
	 * Id_ctr_padre de Centro
	 *
	 * @var integer
	 */
	 protected $iid_ctr_padre;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

 	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_ubi = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_ubi' => $this->iid_ubi);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_centros');
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
		$aDades['tipo_ubi'] = $this->stipo_ubi;
		$aDades['nombre_ubi'] = $this->snombre_ubi;
		$aDades['dl'] = $this->sdl;
		$aDades['pais'] = $this->spais;
		$aDades['region'] = $this->sregion;
		$aDades['status'] = $this->bstatus;
		$aDades['f_status'] = $this->df_status;
		$aDades['sv'] = $this->bsv;
		$aDades['sf'] = $this->bsf;
		$aDades['tipo_ctr'] = $this->stipo_ctr;
		$aDades['tipo_labor'] = $this->itipo_labor;
		$aDades['cdc'] = $this->bcdc;
		$aDades['id_ctr_padre'] = $this->iid_ctr_padre;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['status']) || ($aDades['status'] === 'off') || ($aDades['status'] === 'false') || ($aDades['status'] === 'f')) { $aDades['status']='f'; } else { $aDades['status']='t'; }
		if (empty($aDades['sv']) || ($aDades['sv'] === 'off') || ($aDades['sv'] === 'false') || ($aDades['sv'] === 'f')) { $aDades['sv']='f'; } else { $aDades['sv']='t'; }
		if (empty($aDades['sf']) || ($aDades['sf'] === 'off') || ($aDades['sf'] === 'false') || ($aDades['sf'] === 'f')) { $aDades['sf']='f'; } else { $aDades['sf']='t'; }
		if (empty($aDades['cdc']) || ($aDades['cdc'] === 'off') || ($aDades['cdc'] === 'false') || ($aDades['cdc'] === 'f')) { $aDades['cdc']='f'; } else { $aDades['cdc']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					status                   = :status,
					f_status                 = :f_status,
					sv                       = :sv,
					sf                       = :sf,
					tipo_ctr                 = :tipo_ctr,
					tipo_labor               = :tipo_labor,
					cdc                      = :cdc";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'Centro.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Centro.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_ubi);
			$campos="(tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc)";
			$valores="(:tipo_ubi,:id_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Centro.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Centro.insertar.execute';
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
		if (isset($this->iid_ubi)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'Centro.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
			$sClauError = 'Centro.eliminar';
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
		if (array_key_exists('tipo_ubi',$aDades)) $this->setTipo_ubi($aDades['tipo_ubi']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('nombre_ubi',$aDades)) $this->setNombre_ubi($aDades['nombre_ubi']);
		if (array_key_exists('dl',$aDades)) $this->setDl($aDades['dl']);
		if (array_key_exists('pais',$aDades)) $this->setPais($aDades['pais']);
		if (array_key_exists('region',$aDades)) $this->setRegion($aDades['region']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
		if (array_key_exists('f_status',$aDades)) $this->setF_status($aDades['f_status']);
		if (array_key_exists('sv',$aDades)) $this->setSv($aDades['sv']);
		if (array_key_exists('sf',$aDades)) $this->setSf($aDades['sf']);
		if (array_key_exists('tipo_ctr',$aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
		if (array_key_exists('tipo_labor',$aDades)) $this->setTipo_labor($aDades['tipo_labor']);
		if (array_key_exists('cdc',$aDades)) $this->setCdc($aDades['cdc']);
		if (array_key_exists('id_ctr_padre',$aDades)) $this->setId_ctr_padre($aDades['id_ctr_padre']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut stipo_ctr de Centro
	 *
	 * @return string stipo_ctr
	 */
	function getTipo_ctr() {
		if (!isset($this->stipo_ctr)) {
			$this->DBCarregar();
		}
		return $this->stipo_ctr;
	}
	/**
	 * estableix el valor de l'atribut stipo_ctr de Centro
	 *
	 * @param string stipo_ctr='' optional
	 */
	function setTipo_ctr($stipo_ctr='') {
		$this->stipo_ctr = $stipo_ctr;
	}
	/**
	 * Recupera l'atribut itipo_labor de Centro
	 *
	 * @return integer itipo_labor
	 */
	function getTipo_labor() {
		if (!isset($this->itipo_labor)) {
			$this->DBCarregar();
		}
		return $this->itipo_labor;
	}
	/**
	 * estableix el valor de l'atribut itipo_labor de Centro
	 *
	 * @param integer itipo_labor='' optional
	 */
	function setTipo_labor($itipo_labor='') {
		$this->itipo_labor = $itipo_labor;
	}
	/**
	 * Recupera l'atribut bcdc de Centro
	 *
	 * @return boolean bcdc
	 */
	function getCdc() {
		if (!isset($this->bcdc)) {
			$this->DBCarregar();
		}
		return $this->bcdc;
	}
	/**
	 * estableix el valor de l'atribut bcdc de Centro
	 *
	 * @param boolean bcdc='f' optional
	 */
	function setCdc($bcdc='f') {
		$this->bcdc = $bcdc;
	}
	/**
	 * Recupera l'atribut iid_ctr_padre de Centro
	 *
	 * @return integer iid_ctr_padre
	 */
	function getId_ctr_padre() {
		if (!isset($this->iid_ctr_padre)) {
			$this->DBCarregar();
		}
		return $this->iid_ctr_padre;
	}
	/**
	 * estableix el valor de l'atribut iid_ctr_padre de Centro
	 *
	 * @param integer iid_ctr_padre='' optional
	 */
	function setId_ctr_padre($iid_ctr_padre='') {
		$this->iid_ctr_padre = $iid_ctr_padre;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCentroSet = new core\Set();
		
		$oCentroSet->add($this->getDatosTipo_ubi());
		$oCentroSet->add($this->getDatosNombre_ubi());
		$oCentroSet->add($this->getDatosDl());
		$oCentroSet->add($this->getDatosPais());
		$oCentroSet->add($this->getDatosRegion());
		$oCentroSet->add($this->getDatosStatus());
		$oCentroSet->add($this->getDatosF_status());
		$oCentroSet->add($this->getDatosSv());
		$oCentroSet->add($this->getDatosSf());
		$oCentroSet->add($this->getDatosTipo_ctr());
		$oCentroSet->add($this->getDatosTipo_labor());
		$oCentroSet->add($this->getDatosCdc());
		$oCentroSet->add($this->getDatosId_ctr_padre());
		return $oCentroSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut stipo_ctr de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo_ctr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_ctr'));
		$oDatosCampo->setEtiqueta(_("tipo_ctr"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itipo_labor de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo_labor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_labor'));
		$oDatosCampo->setEtiqueta(_("tipo_labor"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bcdc de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCdc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cdc'));
		$oDatosCampo->setEtiqueta(_("cdc"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_ctr_padre de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_ctr_padre() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ctr_padre'));
		$oDatosCampo->setEtiqueta(_("id_ctr_padre"));
		return $oDatosCampo;
	}
}
?>
