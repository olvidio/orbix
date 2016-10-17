<?php
namespace permisos\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula m0_mods_installed_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
/**
 * Classe que implementa l'entitat m0_mods_installed_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
class ModuloInstalado Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ModuloInstalado
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ModuloInstalado
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_mod de ModuloInstalado
	 *
	 * @var integer
	 */
	 private $iid_mod;
	/**
	 * Status de ModuloInstalado
	 *
	 * @var boolean
	 */
	 private $bstatus;
	/**
	 * Param de ModuloInstalado
	 *
	 * @var string
	 */
	 private $sparam;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ModuloInstalado
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ModuloInstalado
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
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_mod') && $val_id !== '') $this->iid_mod = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_mod = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_mod' => $this->iid_mod);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('m0_mods_installed_dl');
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
		$aDades['status'] = $this->bstatus;
		$aDades['param'] = $this->sparam;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if (empty($aDades['status']) || ($aDades['status'] === 'off') || ($aDades['status'] === false) || ($aDades['status'] === 'f')) { $aDades['status']='f'; } else { $aDades['status']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					status                   = :status,
					param                    = :param";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_mod='$this->iid_mod'")) === false) {
				$sClauError = 'ModuloInstalado.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'ModuloInstalado.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_mod);
			$campos="(id_mod,status,param)";
			$valores="(:id_mod,:status,:param)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ModuloInstalado.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'ModuloInstalado.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
		if (isset($this->iid_mod)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_mod='$this->iid_mod'")) === false) {
				$sClauError = 'ModuloInstalado.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$qRs->rowCount()) return false;
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_mod='$this->iid_mod'")) === false) {
			$sClauError = 'ModuloInstalado.eliminar';
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
		if (array_key_exists('id_mod',$aDades)) $this->setId_mod($aDades['id_mod']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
		if (array_key_exists('param',$aDades)) $this->setParam($aDades['param']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ModuloInstalado en un array
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
	 * Recupera las claus primàries de ModuloInstalado en un array
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
	 * Recupera l'atribut iid_mod de ModuloInstalado
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
	 * estableix el valor de l'atribut iid_mod de ModuloInstalado
	 *
	 * @param integer iid_mod
	 */
	function setId_mod($iid_mod) {
		$this->iid_mod = $iid_mod;
	}
	/**
	 * Recupera l'atribut bstatus de ModuloInstalado
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
	 * estableix el valor de l'atribut bstatus de ModuloInstalado
	 *
	 * @param boolean bstatus='f' optional
	 */
	function setStatus($bstatus='f') {
		$this->bstatus = $bstatus;
	}
	/**
	 * Recupera l'atribut sparam de ModuloInstalado
	 *
	 * @return string sparam
	 */
	function getParam() {
		if (!isset($this->sparam)) {
			$this->DBCarregar();
		}
		return $this->sparam;
	}
	/**
	 * estableix el valor de l'atribut sparam de ModuloInstalado
	 *
	 * @param string sparam='' optional
	 */
	function setParam($sparam='') {
		$this->sparam = $sparam;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oModuloInstaladoSet = new core\Set();

		$oModuloInstaladoSet->add($this->getDatosId_mod());
		$oModuloInstaladoSet->add($this->getDatosStatus());
		$oModuloInstaladoSet->add($this->getDatosParam());
		return $oModuloInstaladoSet->getTot();
	}

	function getDatosDesc_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'modulo'));
		$oDatosCampo->setEtiqueta(_("descripcion"));
		$oDatosCampo->setTipo('depende');
		$oDatosCampo->setArgument('devel\model\Modulo');
		$oDatosCampo->setArgument2('nom');
		$oDatosCampo->setArgument3('getListaModulos');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bstatus de ModuloInstalado
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosStatus() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'status'));
		$oDatosCampo->setEtiqueta(_("Activo"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sparam de ModuloInstalado
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosParam() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'param'));
		$oDatosCampo->setEtiqueta(_("parametros"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument('50');
		return $oDatosCampo;
	}
}
?>
