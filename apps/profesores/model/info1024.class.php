<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1024 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("congresos a los que ha asistido una persona"));
		$this->setTxtEliminar();
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\ProfesorCongreso');
		$this->setMetodoGestor('getProfesorCongresos');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1024;
	}
}