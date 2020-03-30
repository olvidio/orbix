<?php
namespace usuarios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula aux_roles
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/01/2014
 */
/**
 * Classe que implementa l'entitat aux_roles
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/01/2014
 */
class Role Extends core\ClasePropiedades {
    
    // pau constants.
    const PAU_CDC		 = 'cdc'; // Casa.
	const PAU_CTR 	  	 = 'ctr'; // Centro.
	const PAU_NOM	  	 = 'nom'; // Persona.
	const PAU_SACD 	  	 = 'sacd'; // Sacd.
	
	const ARRAY_PAU_TXT = [
            self::PAU_CDC		 => 'cdc',
            self::PAU_CTR 	  	 => 'ctr',
            self::PAU_NOM	  	 => 'nom',
            self::PAU_SACD 	  	 => 'sacd',
	];
    
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Role
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Role
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_role de Role
	 *
	 * @var integer
	 */
	 private $iid_role;
	/**
	 * Role de Role
	 *
	 * @var string
	 */
	 private $srole;
	/**
	 * Sf de Role
	 *
	 * @var boolean
	 */
	 private $bsf;
	/**
	 * Sv de Role
	 *
	 * @var boolean
	 */
	 private $bsv;
	/**
	 * Pau de Role
	 *
	 * @var string
	 */
	 private $spau;
	/**
	 * Dmz de Role
	 *
	 * @var boolean
	 */
	 private $bdmz;

	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	 
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_role
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_role') && $val_id !== '') $this->iid_role = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_role = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_role' => $this->iid_role);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_roles');
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
		$aDades['role'] = $this->srole;
		$aDades['sf'] = $this->bsf;
		$aDades['sv'] = $this->bsv;
		$aDades['pau'] = $this->spau;
		$aDades['dmz'] = $this->bdmz;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['sf']) ) { $aDades['sf']='true'; } else { $aDades['sf']='false'; }
		if ( core\is_true($aDades['sv']) ) { $aDades['sv']='true'; } else { $aDades['sv']='false'; }
		if ( core\is_true($aDades['dmz']) ) { $aDades['dmz']='true'; } else { $aDades['dmz']='false'; }
		
		if ($bInsert === false) {
			//UPDATE
			$update="
					role                     = :role,
					sf                       = :sf,
					sv                       = :sv,
					pau                      = :pau,
					dmz                      = :dmz";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_role='$this->iid_role'")) === false) {
				$sClauError = 'Role.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Role.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(role,sf,sv,pau,dmz)";
			$valores="(:role,:sf,:sv,:pau,:dmz)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Role.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Role.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_role = $oDbl->lastInsertId('aux_roles_id_role_seq');
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
		if (isset($this->iid_role)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_role='$this->iid_role'")) === false) {
				$sClauError = 'Role.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_role='$this->iid_role'")) === false) {
			$sClauError = 'Role.eliminar';
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
		if (array_key_exists('id_role',$aDades)) $this->setId_role($aDades['id_role']);
		if (array_key_exists('role',$aDades)) $this->setRole($aDades['role']);
		if (array_key_exists('sf',$aDades)) $this->setSf($aDades['sf']);
		if (array_key_exists('sv',$aDades)) $this->setSv($aDades['sv']);
		if (array_key_exists('pau',$aDades)) $this->setPau($aDades['pau']);
		if (array_key_exists('dmz',$aDades)) $this->setDmz($aDades['dmz']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_role('');
		$this->setRole('');
		$this->setSf('');
		$this->setSv('');
		$this->setPau('');
		$this->setDmz('');
		$this->setPrimary_key($aPK);
	}
	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Role en un array
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
	 * Recupera las claus primàries de Role en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_role' => $this->iid_role);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de Role en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_role') && $val_id !== '') $this->iid_role = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_role de Role
	 *
	 * @return integer iid_role
	 */
	function getId_role() {
		if (!isset($this->iid_role)) {
			$this->DBCarregar();
		}
		return $this->iid_role;
	}
	/**
	 * estableix el valor de l'atribut iid_role de Role
	 *
	 * @param integer iid_role
	 */
	function setId_role($iid_role) {
		$this->iid_role = $iid_role;
	}
	/**
	 * Recupera l'atribut srole de Role
	 *
	 * @return string srole
	 */
	function getRole() {
		if (!isset($this->srole)) {
			$this->DBCarregar();
		}
		return $this->srole;
	}
	/**
	 * estableix el valor de l'atribut srole de Role
	 *
	 * @param string srole='' optional
	 */
	function setRole($srole='') {
		$this->srole = $srole;
	}
	/**
	 * Recupera l'atribut bsf de Role
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
	 * estableix el valor de l'atribut bsf de Role
	 *
	 * @param boolean bsf='f' optional
	 */
	function setSf($bsf='f') {
		$this->bsf = $bsf;
	}
	/**
	 * Recupera l'atribut bsv de Role
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
	 * estableix el valor de l'atribut bsv de Role
	 *
	 * @param boolean bsv='f' optional
	 */
	function setSv($bsv='f') {
		$this->bsv = $bsv;
	}
	/**
	 * Recupera l'atribut spau de Role
	 *
	 * @return string spau
	 */
	function getPau() {
		if (!isset($this->spau)) {
			$this->DBCarregar();
		}
		return $this->spau;
	}
	/**
	 * estableix el valor de l'atribut spau de Role
	 *
	 * @param string spau='' optional
	 */
	function setPau($spau='') {
		$this->spau = $spau;
	}
	/**
	 * Recupera l'atribut bdmz de Role
	 *
	 * @return string bdmz
	 */
	function getDmz() {
		if (!isset($this->bdmz)) {
			$this->DBCarregar();
		}
		return $this->bdmz;
	}
	/**
	 * estableix el valor de l'atribut bdmz de Role
	 *
	 * @param string bdmz='' optional
	 */
	function setDmz($bdmz='') {
		$this->bdmz = $bdmz;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oRoleSet = new core\Set();

		$oRoleSet->add($this->getDatosRole());
		$oRoleSet->add($this->getDatosSf());
		$oRoleSet->add($this->getDatosSv());
		$oRoleSet->add($this->getDatosPau());
		$oRoleSet->add($this->getDatosDmz());
		return $oRoleSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut srole de Role
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosRole() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'role'));
		$oDatosCampo->setEtiqueta(_("role"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsf de Role
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosSf() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sf'));
		$oDatosCampo->setEtiqueta(_("sf"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bsv de Role
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosSv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'sv'));
		$oDatosCampo->setEtiqueta(_("sv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spau de Role
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPau() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pau'));
		$oDatosCampo->setEtiqueta(_("pau"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bdmz de Role
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDmz() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dmz'));
		$oDatosCampo->setEtiqueta(_("dmz"));
		return $oDatosCampo;
	}
}
