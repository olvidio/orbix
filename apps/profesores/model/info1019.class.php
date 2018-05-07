<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1019 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier de ampliación de docencia del studium generale"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esta fila?"));
		$this->setTxtBuscar();
		$this->setTxtExplicacion();
		
		$this->setClase('profesores\\model\\entity\\ProfesorAmpliacion');
		$this->setMetodoGestor('getProfesorAmpliaciones');
		$this->setPau('p');
	}
	
	public function getId_dossier() {
		return 1019;
	}
}