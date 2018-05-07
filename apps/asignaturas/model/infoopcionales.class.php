<?php
namespace asignaturas\model;
use core;

/* No vale el underscore en el nombre */
class InfoOpcionales extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("asignaturas opcionales"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esta opcional?"));
		$this->setTxtBuscar(_("buscar una asignatura opcional"));
		$this->setTxtExplicacion();

		$this->setClase('asignaturas\\model\\entity\\Asignatura');
		$this->setMetodoGestor('getAsignaturas');
	}

	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (!empty($this->k_buscar)) {
			$aWhere['nombre_asig']= $this->k_buscar;
			$aOperador['nombre_asig']='sin_acentos';
		}
		$aWhere['id_asignatura']= 3000;
		$aOperador['id_asignatura']='>';
		$aWhere['_ordre']='nombre_corto';
		$oLista=new GestorAsignatura();
		$Coleccion=$oLista->getAsignaturas($aWhere,$aOperador);

		return $Coleccion;
	}
}