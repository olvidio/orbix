<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1018 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier de nombramientos del studium generale"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este nombramiento?"));
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\Profesor');
		$this->setMetodoGestor('getProfesores');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1018;
	}
}