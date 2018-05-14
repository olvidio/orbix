<?php
namespace asignaturas\model;
use core;

/* No vale el underscore en el nombre */
class InfoSectores extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("sectores"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este sector?"));
		$this->setTxtBuscar(_("buscar un sector"));
		$this->setTxtExplicacion();

		$this->setClase('asignaturas\\model\\entity\\Sector');
		$this->setMetodoGestor('getSectores');
	}

	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'sector');
			$aOperador='';
		} else {
			$aWhere=array('sector'=> $this->k_buscar);
			$aOperador=array('sector'=>'sin_acentos');
		}
		$oLista=new entity\GestorSector();
		$Coleccion=$oLista->getSectores($aWhere,$aOperador);

		return $Coleccion;
	}
}