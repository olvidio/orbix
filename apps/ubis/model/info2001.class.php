<?php
namespace ubis\model;
use core;
use web; // necesario para los deplegables de 'depende'


/* No vale el underscore en el nombre */
class Info2001 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("telecomunicaciones de un centro o casa"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esta teleco?"));
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		// No hace falta definir, porque ya se sobreescribe el metodo setObj_pau().
		//$this->setClase('profesores\\model\\ProfesorPublicacion');
		$this->setMetodoGestor('getTelecos');
		$this->setPau('u');
	}
	
	public function getId_dossier() {
		return 2001;
	}
		
	public function setObj_pau($obj_pau) {
		switch ($obj_pau) {
			case 'Centro':
				$this->obj = 'ubis\\model\\entity\\TelecoCtr';
				break;
			case 'CentroDl':
				$this->obj = 'ubis\\model\\entity\\TelecoCtrDl';
				break;
			case 'CentroEx':
				$this->obj = 'ubis\\model\\entity\\TelecoCtrEx';
				break;
			case 'Casa':
				$this->obj = 'ubis\\model\\entity\\TelecoCdc';
				break;
			case 'CasaDl':
				$this->obj = 'ubis\\model\\entity\\TelecoCdcDl';
				break;
			case 'CasaEx':
				$this->obj = 'ubis\\model\\entity\\TelecoCdcEx';
				break;
		}
	}
	
	public function getDespl_depende() {
		$oFicha =  $this->getFicha();
		$despl_depende = "<option></option>";
		// para el desplegable depende
		$v1=$oFicha->tipo_teleco;	
		$v2=$oFicha->desc_teleco;	
		if (!empty($v2)) {
			$oDepende = new entity\GestorDescTeleco();
			$aOpciones=$oDepende->getListaDescTelecoUbis($v1);
			$oDesplegable=new web\Desplegable('',$aOpciones,$v2,true);
			$despl_depende = $oDesplegable->options();
		} else {
			$despl_depende = "<option></option>";
		}
		return $despl_depende;
	}

	public function getAccion($valor_depende){
		//caso de actualizar el campo depende
		if (isset($this->accion)) {
			if ($this->accion == 'desc_teleco') {
				$oDepende = new entity\GestorDescTeleco();
				$aOpciones = $oDepende->getListaDescTelecoUbis($valor_depende);
				$oDesplegable = new web\Desplegable('',$aOpciones,'',true);
				$despl_depende = $oDesplegable->options();
			}
		}
		
		return $despl_depende;
	}
}
