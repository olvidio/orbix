<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat u_centros_ex
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */

class CentroEx Extends Centro {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Id_auto de CentroEx
	 *
	 * @var integer
	 */
	 protected $iid_auto;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array $iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBR'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_ubi = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_ubi' => $this->iid_ubi);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_centros_ex');
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
		if ( core\is_true($aDades['status']) ) { $aDades['status']='true'; } else { $aDades['status']='false'; }
		if ( core\is_true($aDades['sv']) ) { $aDades['sv']='true'; } else { $aDades['sv']='false'; }
		if ( core\is_true($aDades['sf']) ) { $aDades['sf']='true'; } else { $aDades['sf']='false'; }
		if ( core\is_true($aDades['cdc']) ) { $aDades['cdc']='true'; } else { $aDades['cdc']='false'; }
		
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
					cdc                      = :cdc,
					id_ctr_padre              = :id_ctr_padre";

			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'CentroEx.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'CentroEx.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos = "(tipo_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre)";
			$valores = "(:tipo_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc,:id_ctr_padre)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'CentroEx.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'CentroEx.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_auto'] = $oDbl->lastInsertId('u_centros_ex_id_auto_seq');
			$aDades['id_ubi'] = $oDbl->query("SELECT id_ubi FROM $nom_tabla WHERE id_auto =".$aDades['id_auto'])->fetchColumn();
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
				$sClauError = 'CentroEx.carregar';
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
			$sClauError = 'CentroEx.eliminar';
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
		$aPK = $this->getPrimary_key();
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
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/
}
?>
