<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat u_cdc_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */

class CasaDl Extends Casa {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Id_auto de CasaDlDl
	 *
	 * @var integer
	 */
	 private $iid_auto;
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
		$oDbl = $GLOBALS['oDBC'];
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
		$this->setNomTabla('u_cdc_dl');
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
		$aDades['tipo_casa'] = $this->stipo_casa;
		$aDades['plazas'] = $this->iplazas;
		$aDades['num_sacd'] = $this->inum_sacd;
		$aDades['biblioteca'] = $this->sbiblioteca;
		$aDades['observ'] = $this->sobserv;
		$aDades['plazas_min'] = $this->iplazas_min;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['status']) || ($aDades['status'] === 'off') || ($aDades['status'] === 'false') || ($aDades['status'] === 'f')) { $aDades['status']='f'; } else { $aDades['status']='t'; }
		if (empty($aDades['sv']) || ($aDades['sv'] === 'off') || ($aDades['sv'] === 'false') || ($aDades['sv'] === 'f')) { $aDades['sv']='f'; } else { $aDades['sv']='t'; }
		if (empty($aDades['sf']) || ($aDades['sf'] === 'off') || ($aDades['sf'] === 'false') || ($aDades['sf'] === 'f')) { $aDades['sf']='f'; } else { $aDades['sf']='t'; }

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
					tipo_casa                = :tipo_casa,
					plazas                   = :plazas,
					num_sacd                 = :num_sacd,
					biblioteca               = :biblioteca,
					observ                   = :observ,
					plazas_min               = :plazas_min";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'CasaDl.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'CasaDl.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(tipo_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_casa,plazas,num_sacd,biblioteca,observ,plazas_min)";
			$valores="(:tipo_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_casa,:plazas,:num_sacd,:biblioteca,:observ,:plazas_min)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'CasaDl.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'CasaDl.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_auto'] = $oDbl->lastInsertId('u_cdc_dl_id_auto_seq');
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
				$sClauError = 'CasaDl.carregar';
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
			$sClauError = 'CasaDl.eliminar';
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
		if (array_key_exists('tipo_casa',$aDades)) $this->setTipo_casa($aDades['tipo_casa']);
		if (array_key_exists('plazas',$aDades)) $this->setPlazas($aDades['plazas']);
		if (array_key_exists('num_sacd',$aDades)) $this->setNum_sacd($aDades['num_sacd']);
		if (array_key_exists('biblioteca',$aDades)) $this->setBiblioteca($aDades['biblioteca']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('id_auto',$aDades)) $this->setId_auto($aDades['id_auto']);
		if (array_key_exists('plazas_min',$aDades)) $this->setPlazas_min($aDades['plazas_min']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut iid_auto de CasaDlDl
	 *
	 * @return integer iid_auto
	 */
	function getId_auto() {
		if (!isset($this->iid_auto)) {
			$this->DBCarregar();
		}
		return $this->iid_auto;
	}
	/**
	 * estableix el valor de l'atribut iid_auto de CasaDlDl
	 *
	 * @param integer iid_auto='' optional
	 */
	function setId_auto($iid_auto='') {
		$this->iid_auto = $iid_auto;
	}
	/**
	 * Recupera l'atribut iplazas_min de CasaDlDl
	 *
	 * @return integer iplazas_min
	 */
	function getPlazas_min() {
		if (!isset($this->iplazas_min)) {
			$this->DBCarregar();
		}
		return $this->iplazas_min;
	}
	/**
	 * estableix el valor de l'atribut iplazas_min de CasaDlDl
	 *
	 * @param integer iplazas_min='' optional
	 */
	function setPlazas_min($iplazas_min='') {
		$this->iplazas_min = $iplazas_min;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

}
?>