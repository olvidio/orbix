<?php
namespace devel\model;
use core;

/* No vale el underscore en el nombre */
class InfoModsInstalled extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("módulos instalados"));
		$this->setTxtEliminar(_("¿Está seguro que desea desinstalar este módulo?"));
		$this->setTxtBuscar(_("buscar un módulo"));
		$this->setTxtExplicacion();

		$this->setClase('devel\\model\\entity\\ModuloInstalado');
		$this->setMetodoGestor('getModulosInstalados');
	}

	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'id_mod');
			$aOperador='';
		} else {
			$aWhere=array('id_mod'=> $this->k_buscar);
		}
		$oLista = new entity\GestorApp();
		$Coleccion=$oLista->getApps($aWhere,$aOperador);

		return $Coleccion;
	}
}