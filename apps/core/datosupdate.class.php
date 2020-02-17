<?php
namespace core;

/**
 * Classe que implementa 
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/04/2018
 */
class DatosUpdate {
	/* ATRIBUTS ----------------------------------------------------------------- */
	
	private $oFicha;
	private $Campos;

	public function eliminar() {
		$oFicha = $this->getFicha();
		if ($oFicha->DBEliminar() === false) {
			$msg_err = _("hay un error, no se ha eliminado");
			return $msg_err;
		}
		return true;	
	}

	public function nuevo() {
		$oFicha = $this->getFicha();
		$aCampos = $this->getCampos();
		foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
			$nom_camp=$oDatosCampo->getNom_camp();	
			$aCampos[$nom_camp]=empty($aCampos[$nom_camp])? '' : $aCampos[$nom_camp];
			$oFicha->$nom_camp=$aCampos[$nom_camp];
		}
		if ($oFicha->DBGuardar() === false) {
			$msg_err = _("hay un error, no se ha guardado");
			return $msg_err;
		}
		return true;	
	}

	public function editar() {
		$aCampos = $this->getCampos();
		$oFicha = $this->getFicha();
		$oFicha->DBCarregar();
		foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
			$nom_camp=$oDatosCampo->getNom_camp();	
			// si es un checkbox y está vacío, no pasa nada
			$tipo=$oDatosCampo->getTipo();	
			if ($tipo=='check' && empty($aCampos[$nom_camp])) $aCampos[$nom_camp]='f';
			// si es con decimales, cambio coma por punto
			if ($tipo=='decimal' && !empty($aCampos[$nom_camp])) $aCampos[$nom_camp]=str_replace(',','.',$aCampos[$nom_camp]);
			$oFicha->$nom_camp=$aCampos[$nom_camp];
		}
		if ($oFicha->DBGuardar() === false) {
			$msg_err = _("hay un error, no se ha guardado");
			return $msg_err;
		}
		return true;;
	}

	public function getFicha() {
		return $this->oFicha;
	}

	public function getCampos() {
		return $this->Campos;
	}

	public function setFicha($oFicha) {
		$this->oFicha = $oFicha;
	}

	public function setCampos($Campos) {
		$this->Campos = $Campos;
	}
}