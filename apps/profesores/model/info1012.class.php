<?php
namespace profesores\model;
use core;

/* No vale el underscore en el nombre */
class Info1012 extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("dossier de publicaciones"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esta publicación?"));
		$this->setTxtBuscar();
		$this->setTxtExplicacion();

		$this->setClase('profesores\\model\\entity\\ProfesorPublicacion');
		$this->setMetodoGestor('getProfesorPublicaciones');
		$this->setPau('p');
	}

	public function getId_dossier() {
		return 1012;
	}
}