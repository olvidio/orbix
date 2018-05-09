<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat xu_region
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class Region Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Region
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Region
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_region de Region
	 *
	 * @var integer
	 */
	 private $iid_region;
	/**
	 * Region de Region
	 *
	 * @var string
	 */
	 private $sregion;
	/**
	 * Nombre_region de Region
	 *
	 * @var string
	 */
	 private $snombre_region;
	/**
	 * Status de Region
	 *
	 * @var boolean
	 */
	 private $bstatus;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array sregion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if ($nom_id === 'region') $nom_id='s'.$nom_id;
				if ($nom_id === 'id_region') $nom_id='i'.$nom_id;
				if ($val_id !== '') $this->$nom_id = $val_id;
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->sregion = $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('sregion' => $this->sregion);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xu_region');
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
		$aDades['id_region'] = $this->iid_region;
		$aDades['region'] = $this->sregion;
		$aDades['nombre_region'] = $this->snombre_region;
		$aDades['status'] = $this->bstatus;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['status']) || ($aDades['status'] === 'off') || ($aDades['status'] === 'false') || ($aDades['status'] === 'f')) { $aDades['status']='f'; } else { $aDades['status']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_region                = :id_region,
					region                	 = :region,
					nombre_region            = :nombre_region,
					status                   = :status";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE region='$this->sregion'")) === false) {
				$sClauError = 'Region.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Region.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->sregion);
			$campos="(id_region,region,nombre_region,status)";
			$valores="(:id_region,:region,:nombre_region,:status)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Region.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Region.insertar.execute';
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
		if (isset($this->sregion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE region='$this->sregion'")) === false) {
				$sClauError = 'Region.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE region='$this->sregion'")) === false) {
			$sClauError = 'Region.eliminar';
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
		if (array_key_exists('id_region',$aDades)) $this->setId_region($aDades['id_region']);
		if (array_key_exists('region',$aDades)) $this->setRegion($aDades['region']);
		if (array_key_exists('nombre_region',$aDades)) $this->setNombre_region($aDades['nombre_region']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Region en un array
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
	 * Recupera las claus primàries de Region en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('sregion' => $this->sregion);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_region de Region
	 *
	 * @return integer iid_region
	 */
	function getId_region() {
		if (!isset($this->iid_region)) {
			$this->DBCarregar();
		}
		return $this->iid_region;
	}
	/**
	 * estableix el valor de l'atribut iid_region de Region
	 *
	 * @param integer iid_region='' optional
	 */
	function setId_region($iid_region='') {
		$this->iid_region = $iid_region;
	}
	/**
	 * Recupera l'atribut sregion de Region
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
	 * estableix el valor de l'atribut sregion de Region
	 *
	 * @param string sregion
	 */
	function setRegion($sregion) {
		$this->sregion = $sregion;
	}
	/**
	 * Recupera l'atribut snombre_region de Region
	 *
	 * @return string snombre_region
	 */
	function getNombre_region() {
		if (!isset($this->snombre_region)) {
			$this->DBCarregar();
		}
		return $this->snombre_region;
	}
	/**
	 * estableix el valor de l'atribut snombre_region de Region
	 *
	 * @param string snombre_region='' optional
	 */
	function setNombre_region($snombre_region='') {
		$this->snombre_region = $snombre_region;
	}
	/**
	 * Recupera l'atribut bstatus de Region
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
	 * estableix el valor de l'atribut bstatus de Region
	 *
	 * @param boolean bstatus='f' optional
	 */
	function setStatus($bstatus='f') {
		$this->bstatus = $bstatus;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oRegionSet = new core\Set();

		$oRegionSet->add($this->getDatosId_region());
		$oRegionSet->add($this->getDatosRegion());
		$oRegionSet->add($this->getDatosNombre_region());
		$oRegionSet->add($this->getDatosStatus());
		return $oRegionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_region de Region
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_region() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_region'));
		$oDatosCampo->setEtiqueta(_("id_region"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(3);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_region de Region
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosRegion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'region'));
		$oDatosCampo->setEtiqueta(_("sigla"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(6);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snombre_region de Region
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNombre_region() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_region'));
		$oDatosCampo->setEtiqueta(_("nombre de la región"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bstatus de Region
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosStatus() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'status'));
		$oDatosCampo->setEtiqueta(_("en activo"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
}
?>