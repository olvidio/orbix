<?php
namespace actividadcargos\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula xd_orden_cargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
/**
 * Classe que implementa l'entitat xd_orden_cargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
class Cargo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de cargo
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de cargo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_cargo de cargo
	 *
	 * @var integer
	 */
	 private $iid_cargo;
	/**
	 * Cargo de cargo
	 *
	 * @var string
	 */
	 private $scargo;
	/**
	 * Orden_cargo de cargo
	 *
	 * @var integer
	 */
	 private $iorden_cargo;
	/**
	 * Sf de cargo
	 *
	 * @var boolean
	 */
	 private $bsf;
	/**
	 * Sv de cargo
	 *
	 * @var boolean
	 */
	 private $bsv;
	/**
	 * Tipo_cargo de cargo
	 *
	 * @var string
	 */
	 private $stipo_cargo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de cargo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de cargo
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
	 * @param integer|array iid_cargo
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_cargo') && $val_id !== '') $this->iid_cargo = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_cargo = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_cargo' => $this->iid_cargo);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xd_orden_cargo');
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
		$aDades['cargo'] = $this->scargo;
		$aDades['orden_cargo'] = $this->iorden_cargo;
		$aDades['sf'] = $this->bsf;
		$aDades['sv'] = $this->bsv;
		$aDades['tipo_cargo'] = $this->stipo_cargo;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['sf'] = ($aDades['sf'] === 't')? 'true' : $aDades['sf'];
		if ( filter_var( $aDades['sf'], FILTER_VALIDATE_BOOLEAN)) { $aDades['sf']='t'; } else { $aDades['sf']='f'; }
		$aDades['sv'] = ($aDades['sv'] === 't')? 'true' : $aDades['sv'];
		if ( filter_var( $aDades['sv'], FILTER_VALIDATE_BOOLEAN)) { $aDades['sv']='t'; } else { $aDades['sv']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					cargo                    = :cargo,
					orden_cargo              = :orden_cargo,
					sf                       = :sf,
					sv                       = :sv,
					tipo_cargo               = :tipo_cargo";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_cargo='$this->iid_cargo'")) === false) {
				$sClauError = 'cargo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'cargo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(cargo,orden_cargo,sf,sv,tipo_cargo)";
			$valores="(:cargo,:orden_cargo,:sf,:sv,:tipo_cargo)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'cargo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'cargo.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_cargo = $oDbl->lastInsertId('xd_orden_cargo_id_cargo_seq');
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
		if (isset($this->iid_cargo)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_cargo='$this->iid_cargo'")) === false) {
				$sClauError = 'cargo.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_cargo='$this->iid_cargo'")) === false) {
			$sClauError = 'cargo.eliminar';
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
		if (array_key_exists('id_cargo',$aDades)) $this->setId_cargo($aDades['id_cargo']);
		if (array_key_exists('cargo',$aDades)) $this->setCargo($aDades['cargo']);
		if (array_key_exists('orden_cargo',$aDades)) $this->setOrden_cargo($aDades['orden_cargo']);
		if (array_key_exists('sf',$aDades)) $this->setSf($aDades['sf']);
		if (array_key_exists('sv',$aDades)) $this->setSv($aDades['sv']);
		if (array_key_exists('tipo_cargo',$aDades)) $this->setTipo_cargo($aDades['tipo_cargo']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_cargo('');
		$this->setCargo('');
		$this->setOrden_cargo('');
		$this->setSf('');
		$this->setSv('');
		$this->setTipo_cargo('');
	}



	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de cargo en un array
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
	 * Recupera las claus primàries de cargo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_cargo' => $this->iid_cargo);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_cargo de cargo
	 *
	 * @return integer iid_cargo
	 */
	function getId_cargo() {
		if (!isset($this->iid_cargo)) {
			$this->DBCarregar();
		}
		return $this->iid_cargo;
	}
	/**
	 * estableix el valor de l'atribut iid_cargo de cargo
	 *
	 * @param integer iid_cargo
	 */
	function setId_cargo($iid_cargo) {
		$this->iid_cargo = $iid_cargo;
	}
	/**
	 * Recupera l'atribut scargo de cargo
	 *
	 * @return string scargo
	 */
	function getCargo() {
		if (!isset($this->scargo)) {
			$this->DBCarregar();
		}
		return $this->scargo;
	}
	/**
	 * estableix el valor de l'atribut scargo de cargo
	 *
	 * @param string scargo='' optional
	 */
	function setCargo($scargo='') {
		$this->scargo = $scargo;
	}
	/**
	 * Recupera l'atribut iorden_cargo de cargo
	 *
	 * @return integer iorden_cargo
	 */
	function getOrden_cargo() {
		if (!isset($this->iorden_cargo)) {
			$this->DBCarregar();
		}
		return $this->iorden_cargo;
	}
	/**
	 * estableix el valor de l'atribut iorden_cargo de cargo
	 *
	 * @param integer iorden_cargo='' optional
	 */
	function setOrden_cargo($iorden_cargo='') {
		$this->iorden_cargo = $iorden_cargo;
	}
	/**
	 * Recupera l'atribut bsf de cargo
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
	 * estableix el valor de l'atribut bsf de cargo
	 *
	 * @param boolean bsf='f' optional
	 */
	function setSf($bsf='f') {
		$this->bsf = $bsf;
	}
	/**
	 * Recupera l'atribut bsv de cargo
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
	 * estableix el valor de l'atribut bsv de cargo
	 *
	 * @param boolean bsv='f' optional
	 */
	function setSv($bsv='f') {
		$this->bsv = $bsv;
	}
	/**
	 * Recupera l'atribut stipo_cargo de cargo
	 *
	 * @return string stipo_cargo
	 */
	function getTipo_cargo() {
		if (!isset($this->stipo_cargo)) {
			$this->DBCarregar();
		}
		return $this->stipo_cargo;
	}
	/**
	 * estableix el valor de l'atribut stipo_cargo de cargo
	 *
	 * @param string stipo_cargo='' optional
	 */
	function setTipo_cargo($stipo_cargo='') {
		$this->stipo_cargo = $stipo_cargo;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$ocargoSet = new core\Set();

		$ocargoSet->add($this->getDatosCargo());
		$ocargoSet->add($this->getDatosOrden_cargo());
		$ocargoSet->add($this->getDatosSf());
		$ocargoSet->add($this->getDatosSv());
		$ocargoSet->add($this->getDatosTipo_cargo());
		return $ocargoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut scargo de cargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosCargo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cargo'));
		$oDatosCampo->setEtiqueta(_("cargo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iorden_cargo de cargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosOrden_cargo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'orden_cargo'));
		$oDatosCampo->setEtiqueta(_("orden cargo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsf de cargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosSf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sf'));
		$oDatosCampo->setEtiqueta(_("sf"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsv de cargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosSv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sv'));
		$oDatosCampo->setEtiqueta(_("sv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stipo_cargo de cargo
	 * en una clase del tipus DatosCampo
	 *
	 * @return object DatosCampo
	 */
	function getDatosTipo_cargo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_cargo'));
		$oDatosCampo->setEtiqueta(_("tipo de cargo"));
		return $oDatosCampo;
	}
}
?>
