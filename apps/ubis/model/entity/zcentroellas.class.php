<?php
namespace ubis\model\entity;
use core;
use web;
/**
 * Classe que implementa l'entitat u_centros
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */

class CentroEllas Extends Centro {
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
		$oDbl = $GLOBALS['oDBEP'];
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
		$aDades['status'] = ($aDades['status'] === 't')? 'true' : $aDades['status'];
		if ( filter_var( $aDades['status'], FILTER_VALIDATE_BOOLEAN)) { $aDades['status']='t'; } else { $aDades['status']='f'; }
		$aDades['sv'] = ($aDades['sv'] === 't')? 'true' : $aDades['sv'];
		if ( filter_var( $aDades['sv'], FILTER_VALIDATE_BOOLEAN)) { $aDades['sv']='t'; } else { $aDades['sv']='f'; }
		$aDades['sf'] = ($aDades['sf'] === 't')? 'true' : $aDades['sf'];
		if ( filter_var( $aDades['sf'], FILTER_VALIDATE_BOOLEAN)) { $aDades['sf']='t'; } else { $aDades['sf']='f'; }
		$aDades['cdc'] = ($aDades['cdc'] === 't')? 'true' : $aDades['cdc'];
		if ( filter_var( $aDades['cdc'], FILTER_VALIDATE_BOOLEAN)) { $aDades['cdc']='t'; } else { $aDades['cdc']='f'; }

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
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
			$sClauError = 'Centro.eliminar';
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
	function setAllAtributes($aDades,$convert=FALSE) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('tipo_ubi',$aDades)) $this->setTipo_ubi($aDades['tipo_ubi']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('nombre_ubi',$aDades)) $this->setNombre_ubi($aDades['nombre_ubi']);
		if (array_key_exists('dl',$aDades)) $this->setDl($aDades['dl']);
		if (array_key_exists('pais',$aDades)) $this->setPais($aDades['pais']);
		if (array_key_exists('region',$aDades)) $this->setRegion($aDades['region']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
		if (array_key_exists('f_status',$aDades)) $this->setF_status($aDades['f_status'],$convert);
		if (array_key_exists('sv',$aDades)) $this->setSv($aDades['sv']);
		if (array_key_exists('sf',$aDades)) $this->setSf($aDades['sf']);
		if (array_key_exists('tipo_ctr',$aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
		if (array_key_exists('tipo_labor',$aDades)) $this->setTipo_labor($aDades['tipo_labor']);
		if (array_key_exists('cdc',$aDades)) $this->setCdc($aDades['cdc']);
		if (array_key_exists('id_ctr_padre',$aDades)) $this->setId_ctr_padre($aDades['id_ctr_padre']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_schema('');
		$this->setTipo_ubi('');
		$this->setId_ubi('');
		$this->setNombre_ubi('');
		$this->setDl('');
		$this->setPais('');
		$this->setRegion('');
		$this->setStatus('');
		$this->setF_status('');
		$this->setSv('');
		$this->setSf('');
		$this->setTipo_ctr('');
		$this->setTipo_labor('');
		$this->setCdc('');
		$this->setId_ctr_padre('');
	}



	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Centro en un array
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
	 * Recupera l'atribut stipo_ubi de Centro
	 *
	 * @return string stipo_ubi
	 */
	function getTipo_ubi() {
		if (!isset($this->stipo_ubi)) {
			$this->DBCarregar();
		}
		return $this->stipo_ubi;
	}
	/**
	 * estableix el valor de l'atribut stipo_ubi de Centro
	 *
	 * @param string stipo_ubi='' optional
	 */
	function setTipo_ubi($stipo_ubi='') {
		$this->stipo_ubi = $stipo_ubi;
	}
	/**
	 * Recupera l'atribut iid_ubi de Centro
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
	 * estableix el valor de l'atribut iid_ubi de Centro
	 *
	 * @param integer iid_ubi
	 */
	function setId_ubi($iid_ubi) {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut snombre_ubi de Centro
	 *
	 * @return string snombre_ubi
	 */
	function getNombre_ubi() {
		if (!isset($this->snombre_ubi)) {
			$this->DBCarregar();
		}
		return $this->snombre_ubi;
	}
	/**
	 * estableix el valor de l'atribut snombre_ubi de Centro
	 *
	 * @param string snombre_ubi='' optional
	 */
	function setNombre_ubi($snombre_ubi='') {
		$this->snombre_ubi = $snombre_ubi;
	}
	/**
	 * Recupera l'atribut sdl de Centro
	 *
	 * @return string sdl
	 */
	function getDl() {
		if (!isset($this->sdl)) {
			$this->DBCarregar();
		}
		return $this->sdl;
	}
	/**
	 * estableix el valor de l'atribut sdl de Centro
	 *
	 * @param string sdl='' optional
	 */
	function setDl($sdl='') {
		$this->sdl = $sdl;
	}
	/**
	 * Recupera l'atribut spais de Centro
	 *
	 * @return string spais
	 */
	function getPais() {
		if (!isset($this->spais)) {
			$this->DBCarregar();
		}
		return $this->spais;
	}
	/**
	 * estableix el valor de l'atribut spais de Centro
	 *
	 * @param string spais='' optional
	 */
	function setPais($spais='') {
		$this->spais = $spais;
	}
	/**
	 * Recupera l'atribut sregion de Centro
	 *
	 * @return string sregion
	 */
	function getRegion() {
		if (!isset($this->sregion)) {
			$this->DBCarregar();
		}
		return $this->sregion;
	}
	/**
	 * estableix el valor de l'atribut sregion de Centro
	 *
	 * @param string sregion='' optional
	 */
	function setRegion($sregion='') {
		$this->sregion = $sregion;
	}
	/**
	 * Recupera l'atribut bstatus de Centro
	 *
	 * @return boolean bstatus
	 */
	function getStatus() {
		if (!isset($this->bstatus)) {
			$this->DBCarregar();
		}
		return $this->bstatus;
	}
	/**
	 * estableix el valor de l'atribut bstatus de Centro
	 *
	 * @param boolean bstatus='f' optional
	 */
	function setStatus($bstatus='f') {
		$this->bstatus = $bstatus;
	}
	/**
	 * Recupera l'atribut df_status de Centro
	 *
	 * @return web\DateTimeLocal df_status
	 */
	function getF_status() {
	    if (!isset($this->df_status)) {
	        $this->DBCarregar();
	    }
	    if (empty($this->df_status)) {
	    	return new web\NullDateTimeLocal();
	    }
	    $oConverter = new core\Converter('date', $this->df_status);
	    return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_status de Centro
	* Si df_status es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
	* Si convert es false, df_status debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	*
	* @param date|string df_status='' optional.
	* @param boolean convert=true optional. Si es false, df_status debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_status($df_status='',$convert=true) {
		if ($convert === true && !empty($df_status)) {
	        $oConverter = new core\Converter('date', $df_status);
	        $this->df_status =$oConverter->toPg();
	    } else {
	        $this->df_status = $df_status;
	    }
	}
	/**
	 * Recupera l'atribut bsv de Centro
	 *
	 * @return boolean bsv
	 */
	function getSv() {
		if (!isset($this->bsv)) {
			$this->DBCarregar();
		}
		return $this->bsv;
	}
	/**
	 * estableix el valor de l'atribut bsv de Centro
	 *
	 * @param boolean bsv='f' optional
	 */
	function setSv($bsv='f') {
		$this->bsv = $bsv;
	}
	/**
	 * Recupera l'atribut bsf de Centro
	 *
	 * @return boolean bsf
	 */
	function getSf() {
		if (!isset($this->bsf)) {
			$this->DBCarregar();
		}
		return $this->bsf;
	}
	/**
	 * estableix el valor de l'atribut bsf de Centro
	 *
	 * @param boolean bsf='f' optional
	 */
	function setSf($bsf='f') {
		$this->bsf = $bsf;
	}
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
}