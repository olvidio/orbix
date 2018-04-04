<?php
namespace usuarios\model;
use core;
/**
 * Clase treballar amb Grups i Usuaris a l'hora.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 21/10/2010
 */
class GrupoOUsuario Extends Grupo {

	/* METODES PUBLICS ----------------------------------------------------------*/
	
	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregarTot($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_usuario)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
				$sClauError = 'Grupo.carregar';
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
			echo " i donçs";
		   	return false;
		}
	}


	/**
	 * Recupera l'atribut iid_usuario de Grupo
	 *
	 * @return integer iid_usuario
	 */
	function getId_usuario() {
		if (!isset($this->iid_usuario)) {
			$this->DBCarregarTot();
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
			$this->DBCarregarTot();
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
	 * Recupera l'atribut isfsv de Grupo
	 *
	 * @return integer isfsv
	 */
	function getSfsv() {
		if (!isset($this->isfsv)) {
			$this->DBCarregarTot();
		}
		return $this->isfsv;
	}
	/**
	 * estableix el valor de l'atribut isfsv de Grupo
	 *
	 * @param integer isfsv='' optional
	 */
	function setSfsv($isfsv='') {
		$this->isfsv = $isfsv;
	}
}
?>
