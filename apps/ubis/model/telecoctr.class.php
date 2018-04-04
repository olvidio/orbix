<?php
namespace ubis\model;
use core;
/**
 * Classe que implementa l'entitat public(v|f).d_teleco_ctr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class TelecoCtr Extends TelecoUbiGlobal {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_item,iid_ubi
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
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_teleco_ctr');
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
		$aDades['tipo_teleco'] = $this->stipo_teleco;
		$aDades['desc_teleco'] = $this->sdesc_teleco;
		$aDades['num_teleco'] = $this->snum_teleco;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					tipo_teleco              = :tipo_teleco,
					desc_teleco              = :desc_teleco,
					num_teleco               = :num_teleco,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item' AND id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'TelecoCtr.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TelecoCtr.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_ubi);
			$campos="(id_ubi,tipo_teleco,desc_teleco,num_teleco,observ)";
			$valores="(:id_ubi,:tipo_teleco,:desc_teleco,:num_teleco,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TelecoCtr.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TelecoCtr.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_item'] = $oDbl->lastInsertId('d_teleco_ubis_id_item_seq');
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
		if (isset($this->iid_item) && isset($this->iid_ubi)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item' AND id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'TelecoCtr.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === false) {
			$sClauError = 'TelecoCtr.eliminar';
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
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('tipo_teleco',$aDades)) $this->setTipo_teleco($aDades['tipo_teleco']);
		if (array_key_exists('desc_teleco',$aDades)) $this->setDesc_teleco($aDades['desc_teleco']);
		if (array_key_exists('num_teleco',$aDades)) $this->setNum_teleco($aDades['num_teleco']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
