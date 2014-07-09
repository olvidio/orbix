<?php
namespace usuarios\model;
use core;
/**
 * Classe que implementa l'entitat aux_cross_usuarios_grupos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/10/2010
 */

class UsuarioGrupo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de UsuarioGrupo
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de UsuarioGrupo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_usuario de UsuarioGrupo
	 *
	 * @var integer
	 */
	 private $iid_usuario;
	/**
	 * Id_grupo de UsuarioGrupo
	 *
	 * @var integer
	 */
	 private $iid_grupo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_usuario,iid_grupo
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
		$this->setNomTabla('aux_cross_usuarios_grupos');
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
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
";
			if (($qRs = $oDbl->prepare("UPDATE aux_cross_usuarios_gruposs SET $update WHERE id_usuario='$this->iid_usuario' AND id_grupo='$this->iid_grupo'")) === false) {
				$sClauError = 'UsuarioGrupo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'UsuarioGrupo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_usuario, $this->iid_grupo);
			$campos="(id_usuario,id_grupo)";
			$valores="(:id_usuario,:id_grupo)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'UsuarioGrupo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'UsuarioGrupo.insertar.execute';
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
		if (isset($this->iid_usuario) && isset($this->iid_grupo)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_usuario='$this->iid_usuario' AND id_grupo='$this->iid_grupo'")) === false) {
				$sClauError = 'UsuarioGrupo.carregar';
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_usuario='$this->iid_usuario' AND id_grupo='$this->iid_grupo'")) === false) {
			$sClauError = 'UsuarioGrupo.eliminar';
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
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('id_grupo',$aDades)) $this->setId_grupo($aDades['id_grupo']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de UsuarioGrupo en un array
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
	 * Recupera las claus primàries de UsuarioGrupo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_usuario,iid_grupo' => $this->iid_usuario,iid_grupo);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_usuario de UsuarioGrupo
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
	 * estableix el valor de l'atribut iid_usuario de UsuarioGrupo
	 *
	 * @param integer iid_usuario
	 */
	function setId_usuario($iid_usuario) {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut iid_grupo de UsuarioGrupo
	 *
	 * @return integer iid_grupo
	 */
	function getId_grupo() {
		if (!isset($this->iid_grupo)) {
			$this->DBCarregar();
		}
		return $this->iid_grupo;
	}
	/**
	 * estableix el valor de l'atribut iid_grupo de UsuarioGrupo
	 *
	 * @param integer iid_grupo
	 */
	function setId_grupo($iid_grupo) {
		$this->iid_grupo = $iid_grupo;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oUsuarioGrupoSet = new core\Set();

		return $oUsuarioGrupoSet->getTot();
	}



}
?>
