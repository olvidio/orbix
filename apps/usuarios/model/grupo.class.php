<?php
namespace usuarios\model;
use core;
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 21/10/2010
 */
class Grupo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Grupo
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de Grupo
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_usuario de Grupo
	 *
	 * @var integer
	 */
	 protected $iid_usuario;
	/**
	 * Usuario de Grupo
	 *
	 * @var string
	 */
	 protected $susuario;
	/**
	 * Id_role de Usuario
	 *
	 * @var integer
	 */
	 private $iid_role;

	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */

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
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_usuario = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_usuario' => $this->iid_usuario);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_grupos_y_usuarios');
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
		$aDades['id_role'] = $this->iid_role;

		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					usuario                  = :usuario,
					id_role                  = :id_role";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_usuario='$this->iid_usuario'")) === false) {
				$sClauError = 'Grupo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Grupo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(usuario,id_role)";
			$valores="(:usuario,:id_role)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Grupo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Grupo.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_usuario'] = $oDbl->lastInsertId('$nom_tabla_id_usuario_seq');
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
			if (($qRs = $oDbl->query("SELECT * FROM ONLY $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
				$sClauError = 'Grupo.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
			$sClauError = 'Grupo.eliminar';
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
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('usuario',$aDades)) $this->setUsuario($aDades['usuario']);
		if (array_key_exists('id_role',$aDades)) $this->setId_role($aDades['id_role']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Grupo en un array
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
	 * Recupera las claus primàries de Grupo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_usuario' => $this->iid_usuario);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_usuario de Grupo
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
	 * estableix el valor de l'atribut iid_usuario de Grupo
	 *
	 * @param integer iid_usuario
	 */
	function setId_usuario($iid_usuario) {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut susuario de Grupo
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
	 * estableix el valor de l'atribut susuario de Grupo
	 *
	 * @param string susuario='' optional
	 */
	function setUsuario($susuario='') {
		$this->susuario = $susuario;
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
		$oGrupoSet = new core\Set();

		$oGrupoSet->add($this->getDatosUsuario());
		$oUsuarioSet->add($this->getDatosId_role());
		return $oGrupoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut susuario de Grupo
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosUsuario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'usuario'));
		$oDatosCampo->setEtiqueta(_("usuario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_role de Usuario
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_role() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_role'));
		$oDatosCampo->setEtiqueta(_("id_role"));
		return $oDatosCampo;
	}
}
?>
