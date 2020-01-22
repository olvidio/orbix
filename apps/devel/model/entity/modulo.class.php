<?php
namespace devel\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula m0_modulos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
/**
 * Classe que implementa l'entitat m0_modulos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
class Modulo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Modulo
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Modulo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_mod de Modulo
	 *
	 * @var integer
	 */
	 private $iid_mod;
	/**
	 * Nom de Modulo
	 *
	 * @var string
	 */
	 private $snom;
	/**
	 * Descripcion de Modulo
	 *
	 * @var string
	 */
	 private $sdescripcion;
	/**
	 * Mods_req de Modulo
	 *
	 * @var string
	 */
	 private $smods_req;
	/**
	 * Apps_req de Modulo
	 *
	 * @var string
	 */
	 private $sapps_req;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Modulo
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Modulo
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
	 * @param integer|array iid_mod
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_mod') && $val_id !== '') $this->iid_mod = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_mod = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_mod' => $this->iid_mod);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('m0_modulos');
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
		$aDades['nom'] = $this->snom;
		$aDades['descripcion'] = $this->sdescripcion;
		$aDades['mods_req'] = $this->smods_req;
		$aDades['apps_req'] = $this->sapps_req;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					nom                      = :nom,
					descripcion              = :descripcion,
					mods_req                 = :mods_req,
					apps_req                 = :apps_req";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_mod='$this->iid_mod'")) === false) {
				$sClauError = 'Modulo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Modulo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(nom,descripcion,mods_req,apps_req)";
			$valores="(:nom,:descripcion,:mods_req,:apps_req)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Modulo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Modulo.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_mod = $oDbl->lastInsertId('m0_modulos_id_mod_seq');
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
		if (isset($this->iid_mod)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_mod='$this->iid_mod'")) === false) {
				$sClauError = 'Modulo.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_mod='$this->iid_mod'")) === false) {
			$sClauError = 'Modulo.eliminar';
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
		if (array_key_exists('id_mod',$aDades)) $this->setId_mod($aDades['id_mod']);
		if (array_key_exists('nom',$aDades)) $this->setNom($aDades['nom']);
		if (array_key_exists('descripcion',$aDades)) $this->setDescripcion($aDades['descripcion']);
		if (array_key_exists('mods_req',$aDades)) $this->setMods_req($aDades['mods_req']);
		if (array_key_exists('apps_req',$aDades)) $this->setApps_req($aDades['apps_req']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_mod('');
		$this->setNom('');
		$this->setDescripcion('');
		$this->setMods_req('');
		$this->setApps_req('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Modulo en un array
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
	 * Recupera las claus primàries de Modulo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_mod' => $this->iid_mod);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_mod de Modulo
	 *
	 * @return integer iid_mod
	 */
	function getId_mod() {
		if (!isset($this->iid_mod)) {
			$this->DBCarregar();
		}
		return $this->iid_mod;
	}
	/**
	 * estableix el valor de l'atribut iid_mod de Modulo
	 *
	 * @param integer iid_mod
	 */
	function setId_mod($iid_mod) {
		$this->iid_mod = $iid_mod;
	}
	/**
	 * Recupera l'atribut snom de Modulo
	 *
	 * @return string snom
	 */
	function getNom() {
		if (!isset($this->snom)) {
			$this->DBCarregar();
		}
		return $this->snom;
	}
	/**
	 * estableix el valor de l'atribut snom de Modulo
	 *
	 * @param string snom='' optional
	 */
	function setNom($snom='') {
		$this->snom = $snom;
	}
	/**
	 * Recupera l'atribut sdescripcion de Modulo
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
	 * estableix el valor de l'atribut sdescripcion de Modulo
	 *
	 * @param string sdescripcion='' optional
	 */
	function setDescripcion($sdescripcion='') {
		$this->sdescripcion = $sdescripcion;
	}
	/**
	 * Recupera l'atribut smods_req de Modulo
	 *
	 * @return string smods_req
	 */
	function getMods_req() {
		if (!isset($this->smods_req)) {
			$this->DBCarregar();
		}
		return $this->smods_req;
	}
	/**
	 * estableix el valor de l'atribut smods_req de Modulo
	 *
	 * @param string smods_req='' optional
	 */
	function setMods_req($smods_req='') {
		$this->smods_req = $smods_req;
	}
	/**
	 * Recupera l'atribut sapps_req de Modulo
	 *
	 * @return string sapps_req
	 */
	function getApps_req() {
		if (!isset($this->sapps_req)) {
			$this->DBCarregar();
		}
		return $this->sapps_req;
	}
	/**
	 * estableix el valor de l'atribut sapps_req de Modulo
	 *
	 * @param string sapps_req='' optional
	 */
	function setApps_req($sapps_req='') {
		$this->sapps_req = $sapps_req;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oModuloSet = new core\Set();

		$oModuloSet->add($this->getDatosNom());
		$oModuloSet->add($this->getDatosDescripcion());
		$oModuloSet->add($this->getDatosMods_req());
		$oModuloSet->add($this->getDatosApps_req());
		return $oModuloSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut snom de Modulo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom'));
		$oDatosCampo->setEtiqueta(_("nombre"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdescripcion de Modulo
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
	 * Recupera les propietats de l'atribut smods_req de Modulo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosMods_req() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'mods_req'));
		$oDatosCampo->setEtiqueta(_("mods requeridos"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sapps_req de Modulo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosApps_req() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'apps_req'));
		$oDatosCampo->setEtiqueta(_("apps requeridas"));
		return $oDatosCampo;
	}
}
?>
