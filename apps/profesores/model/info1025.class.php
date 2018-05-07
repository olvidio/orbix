<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1025 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier de actividad docente"));
		$this->setTxtEliminar();
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\ProfesorDocenciaStgr');
		$this->setMetodoGestor('getProfesorDocenciasStgr');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1025;
	}
}