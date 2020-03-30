<?php
namespace usuarios\model\entity;
use core;
use core\ConfigGlobal;
/**
 * Fitxer amb la Classe que accedeix a la taula aux_usuarios
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/01/2014
 */
/**
 * Classe que implementa l'entitat aux_usuarios
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/01/2014
 */
class Usuario Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Usuario
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Usuario
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_usuario de Usuario
	 *
	 * @var integer
	 */
	 private $iid_usuario;
	/**
	 * Usuario de Usuario
	 *
	 * @var string
	 */
	 private $susuario;
	/**
	 * Password de Usuario
	 *
	 * @var string
	 */
	 private $spassword;
	/**
	 * Email de Usuario
	 *
	 * @var string
	 */
	 private $semail;
	/**
	 * Id_pau de Usuario
	 *
	 * @var string
	 */
	 private $sid_pau;
	/**
	 * Nom_usuario de Usuario
	 *
	 * @var string
	 */
	 private $snom_usuario;
	/**
	 * Id_role de Usuario
	 *
	 * @var integer
	 */
	 private $iid_role;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * aRoles array amb els roles
	 *
	 * @var array
	 */
	 private $aRoles;
	/**
	 * aPauRoles array amb els id_roles i el seu pau.
	 *
	 * @var array
	 */
	 private $aPauRoles;
	 
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_usuario
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_usuario') && $val_id !== '') $this->iid_usuario = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_usuario = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_usuario' => $this->iid_usuario);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_usuarios');
		
		// llista Roles
		$oGesRoles = new GestorRole();
		$this->aRoles = $oGesRoles->getArrayRoles();
	    $this->aPauRoles = $oGesRoles->getArrayRolesPau();
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
		$aDades['usuario'] = $this->susuario;
		$aDades['password'] = $this->spassword;
		$aDades['email'] = $this->semail;
		$aDades['id_pau'] = $this->sid_pau;
		$aDades['nom_usuario'] = $this->snom_usuario;
		$aDades['id_role'] = $this->iid_role;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					usuario                  = :usuario,
					password                 = :password,
					email                    = :email,
					id_pau                   = :id_pau,
					nom_usuario              = :nom_usuario,
					id_role                  = :id_role";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_usuario='$this->iid_usuario'")) === false) {
				$sClauError = 'Usuario.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Usuario.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(usuario,password,email,id_pau,nom_usuario,id_role)";
			$valores="(:usuario,:password,:email,:id_pau,:nom_usuario,:id_role)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Usuario.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Usuario.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_usuario = $oDbl->lastInsertId('aux_grupos_y_usuarios_id_usuario_seq');
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
		if (isset($this->iid_usuario)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
				$sClauError = 'Usuario.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			// Llegeixo el password (la BD només em passa el handler)
			$pass = $aDades['password'];
			$aDades['password'] = fread($pass,2048);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
			$sClauError = 'Usuario.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return true;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	
	public function isRole($nom_role) {
	    $nom_role = strtolower($nom_role);
	    $aRoles = $this->aRoles;
	    if (!empty($aRoles[$nom_role]) && $aRoles[$nom_role] == $this->getId_role()) {
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}
	/**
	 * Devuelve TRUE/FALSE si el Role del usuario actual tiene un pau determinado.
	 * 
	 * @param string $nom_role el tipo pau del role: cdc, ctr, nom
	 * @return boolean
	 */
	public function isRolePau($nom_pau) {
	    $nom_pau = strtolower($nom_pau);
	    $aPauRoles = $this->aPauRoles;
	    if (!empty($aPauRoles[$this->getId_role()]) && $aPauRoles[$this->getId_role()] == $nom_pau) {
	        return TRUE;
	    } else {
	        return FALSE;
	    }
	}
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('usuario',$aDades)) $this->setUsuario($aDades['usuario']);
		if (array_key_exists('password',$aDades)) $this->setPassword($aDades['password']);
		if (array_key_exists('email',$aDades)) $this->setEmail($aDades['email']);
		if (array_key_exists('id_pau',$aDades)) $this->setId_pau($aDades['id_pau']);
		if (array_key_exists('nom_usuario',$aDades)) $this->setNom_usuario($aDades['nom_usuario']);
		if (array_key_exists('id_role',$aDades)) $this->setId_role($aDades['id_role']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_usuario('');
		$this->setUsuario('');
		$this->setPassword('');
		$this->setEmail('');
		$this->setId_pau('');
		$this->setNom_usuario('');
		$this->setId_role('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Usuario en un array
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
	 * Recupera las claus primàries de Usuario en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_usuario' => $this->iid_usuario);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de Usuario en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_usuario') && $val_id !== '') $this->iid_usuario = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_usuario de Usuario
	 *
	 * @return integer iid_usuario
	 */
	function getId_usuario() {
		if (!isset($this->iid_usuario)) {
			$this->DBCarregar();
		}
		return $this->iid_usuario;
	}
	/**
	 * estableix el valor de l'atribut iid_usuario de Usuario
	 *
	 * @param integer iid_usuario
	 */
	function setId_usuario($iid_usuario) {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut susuario de Usuario
	 *
	 * @return string susuario
	 */
	function getUsuario() {
		if (!isset($this->susuario)) {
			$this->DBCarregar();
		}
		return $this->susuario;
	}
	/**
	 * estableix el valor de l'atribut susuario de Usuario
	 *
	 * @param string susuario='' optional
	 */
	function setUsuario($susuario='') {
		$this->susuario = $susuario;
	}
	/**
	 * Recupera l'atribut spassword de Usuario
	 *
	 * @return integer spassword
	 */
	function getPassword() {
		if (!isset($this->spassword)) {
			$this->DBCarregar();
		}
		return $this->spassword;
	}
	/**
	 * estableix el valor de l'atribut spassword de Usuario
	 *
	 * @param integer spassword='' optional
	 */
	function setPassword($spassword='') {
		$this->spassword = $spassword;
	}
	/**
	 * Recupera l'atribut semail de Usuario
	 *
	 * @return string semail
	 */
	function getEmail() {
		if (!isset($this->semail)) {
			$this->DBCarregar();
		}
		return $this->semail;
	}
	/**
	 * estableix el valor de l'atribut semail de Usuario
	 *
	 * @param string semail='' optional
	 */
	function setEmail($semail='') {
		$this->semail = $semail;
	}
	/**
	 * Recupera l'atribut sid_pau de Usuario
	 *
	 * @return string sid_pau
	 */
	function getId_pau() {
		if (!isset($this->sid_pau)) {
			$this->DBCarregar();
		}
		return $this->sid_pau;
	}
	/**
	 * estableix el valor de l'atribut sid_pau de Usuario
	 *
	 * @param string sid_pau='' optional
	 */
	function setId_pau($sid_pau='') {
		$this->sid_pau = $sid_pau;
	}
	/**
	 * Recupera l'atribut snom_usuario de Usuario
	 *
	 * @return string snom_usuario
	 */
	function getNom_usuario() {
		if (!isset($this->snom_usuario)) {
			$this->DBCarregar();
		}
		return $this->snom_usuario;
	}
	/**
	 * estableix el valor de l'atribut snom_usuario de Usuario
	 *
	 * @param string snom_usuario='' optional
	 */
	function setNom_usuario($snom_usuario='') {
		$this->snom_usuario = $snom_usuario;
	}
	/**
	 * Recupera l'atribut iid_role de Usuario
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
	 * estableix el valor de l'atribut iid_role de Usuario
	 *
	 * @param integer iid_role='' optional
	 */
	function setId_role($iid_role='') {
		$this->iid_role = $iid_role;
	}

	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oUsuarioSet = new core\Set();

		$oUsuarioSet->add($this->getDatosUsuario());
		$oUsuarioSet->add($this->getDatosPassword());
		$oUsuarioSet->add($this->getDatosEmail());
		$oUsuarioSet->add($this->getDatosId_pau());
		$oUsuarioSet->add($this->getDatosNom_usuario());
		$oUsuarioSet->add($this->getDatosId_role());
		return $oUsuarioSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut susuario de Usuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosUsuario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'usuario'));
		$oDatosCampo->setEtiqueta(_("usuario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spassword de Usuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPassword() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'password'));
		$oDatosCampo->setEtiqueta(_("password"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut semail de Usuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosEmail() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'email'));
		$oDatosCampo->setEtiqueta(_("email"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sid_pau de Usuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_pau() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_pau'));
		$oDatosCampo->setEtiqueta(_("id_pau"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snom_usuario de Usuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_usuario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_usuario'));
		$oDatosCampo->setEtiqueta(_("nombre de usuario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_role de Usuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_role() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_role'));
		$oDatosCampo->setEtiqueta(_("id_role"));
		return $oDatosCampo;
	}
}
