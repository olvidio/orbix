<?php
namespace menus\model;
use core;

/* No vale el underscore en el nombre */
class InfoMetaMenus extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("metamenus"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este metamenu?"));
		$this->setTxtBuscar(_("buscar un módulo"));
		$this->setTxtExplicacion();

		$this->setClase('menus\\model\\entity\\Metamenu');
		$this->setMetodoGestor('getMetamenus');
	}

	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'modulo,url');
			$aOperador='';
		} else {
			$aWhere=array('modulo'=> $this->k_buscar);
			$aOperador=array('modulo'=>'sin_acentos');
		}
		$oLista = new entity\GestorMetamenu();
		$Coleccion=$oLista->getMetamenus($aWhere,$aOperador);

		return $Coleccion;
	}
}