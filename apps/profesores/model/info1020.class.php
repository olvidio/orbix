<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class info1020 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier de directores de departamento del studium generale"));
		$this->setTxtEliminar();
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\ProfesorDirector');
		$this->setMetodoGestor('getProfesoresDirectores');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1020;
	}
}