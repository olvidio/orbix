<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1022 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier profesores de latín"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esto?"));
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\ProfesorLatin');
		$this->setMetodoGestor('getProfesoresLatin');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1022;
	}
}