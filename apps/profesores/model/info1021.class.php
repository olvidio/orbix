<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1021 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier juramento del studium generale"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esto?"));
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\ProfesorJuramento');
		$this->setMetodoGestor('getProfesorJuramentos');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1021;
	}
}