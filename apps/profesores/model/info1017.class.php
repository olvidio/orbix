<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1017 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier de títulos de postgrado"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este título?"));
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\TituloEst');
		$this->setMetodoGestor('getTitulosEst');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1017;
	}
}
