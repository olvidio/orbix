<?php
namespace procesos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula a_fases
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
/**
 * Classe que implementa l'entitat a_fases
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class ActividadFase Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ActividadFase
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ActividadFase
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_fase de ActividadFase
	 *
	 * @var integer
	 */
	 private $iid_fase;
	/**
	 * Desc_fase de ActividadFase
	 *
	 * @var string
	 */
	 private $sdesc_fase;
	/**
	 * Sf de ActividadFase
	 *
	 * @var boolean
	 */
	 private $bsf;
	/**
	 * Sv de ActividadFase
	 *
	 * @var boolean
	 */
	 private $bsv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ActividadFase
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ActividadFase
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
	 * @param integer|array iid_fase
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_fase') && $val_id !== '') $this->iid_fase = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_fase = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_fase' => $this->iid_fase);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_fases');
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
		$aDades['desc_fase'] = $this->sdesc_fase;
		$aDades['sf'] = $this->bsf;
		$aDades['sv'] = $this->bsv;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( filter_var( $aDades['sf'], FILTER_VALIDATE_BOOLEAN)) { $aDades['sf']='t'; } else { $aDades['sf']='f'; }
		if ( filter_var( $aDades['sv'], FILTER_VALIDATE_BOOLEAN)) { $aDades['sv']='t'; } else { $aDades['sv']='f'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					desc_fase                = :desc_fase,
					sf                       = :sf,
					sv                       = :sv";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_fase='$this->iid_fase'")) === FALSE) {
				$sClauError = 'ActividadFase.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ActividadFase.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(desc_fase,sf,sv)";
			$valores="(:desc_fase,:sf,:sv)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'ActividadFase.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ActividadFase.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_fase = $oDbl->lastInsertId('a_fases_id_fase_seq');
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
		if (isset($this->iid_fase)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_fase='$this->iid_fase'")) === FALSE) {
				$sClauError = 'ActividadFase.carregar';
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
					$this->setAllAtributes($aDades);
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_fase='$this->iid_fase'")) === FALSE) {
			$sClauError = 'ActividadFase.eliminar';
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
		if (array_key_exists('id_fase',$aDades)) $this->setId_fase($aDades['id_fase']);
		if (array_key_exists('desc_fase',$aDades)) $this->setDesc_fase($aDades['desc_fase']);
		if (array_key_exists('sf',$aDades)) $this->setSf($aDades['sf']);
		if (array_key_exists('sv',$aDades)) $this->setSv($aDades['sv']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ActividadFase en un array
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
	 * Recupera las claus primàries de ActividadFase en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_fase' => $this->iid_fase);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_fase de ActividadFase
	 *
	 * @return integer iid_fase
	 */
	function getId_fase() {
		if (!isset($this->iid_fase)) {
			$this->DBCarregar();
		}
		return $this->iid_fase;
	}
	/**
	 * estableix el valor de l'atribut iid_fase de ActividadFase
	 *
	 * @param integer iid_fase
	 */
	function setId_fase($iid_fase) {
		$this->iid_fase = $iid_fase;
	}
	/**
	 * Recupera l'atribut sdesc_fase de ActividadFase
	 *
	 * @return string sdesc_fase
	 */
	function getDesc_fase() {
		if (!isset($this->sdesc_fase)) {
			$this->DBCarregar();
		}
		return $this->sdesc_fase;
	}
	/**
	 * estableix el valor de l'atribut sdesc_fase de ActividadFase
	 *
	 * @param string sdesc_fase='' optional
	 */
	function setDesc_fase($sdesc_fase='') {
		$this->sdesc_fase = $sdesc_fase;
	}
	/**
	 * Recupera l'atribut bsf de ActividadFase
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
	 * estableix el valor de l'atribut bsf de ActividadFase
	 *
	 * @param boolean bsf='f' optional
	 */
	function setSf($bsf='f') {
		$this->bsf = $bsf;
	}
	/**
	 * Recupera l'atribut bsv de ActividadFase
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
	 * estableix el valor de l'atribut bsv de ActividadFase
	 *
	 * @param boolean bsv='f' optional
	 */
	function setSv($bsv='f') {
		$this->bsv = $bsv;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oActividadFaseSet = new core\Set();

		$oActividadFaseSet->add($this->getDatosDesc_fase());
		$oActividadFaseSet->add($this->getDatosSf());
		$oActividadFaseSet->add($this->getDatosSv());
		return $oActividadFaseSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut sdesc_fase de ActividadFase
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDesc_fase() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'desc_fase'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('30');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsf de ActividadFase
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosSf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sf'));
		$oDatosCampo->setEtiqueta(_("sf"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsv de ActividadFase
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosSv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sv'));
		$oDatosCampo->setEtiqueta(_("sv"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
}