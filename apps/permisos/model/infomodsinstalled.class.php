<?php
namespace permisos\model;
use core;

/* No vale el underscore en el nombre */
class InfoModsInstalled extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("módulos instalados"));
		$this->setTxtEliminar(_("¿Está seguro que desea desinstalar este módulo?"));
		$this->setTxtBuscar(_("buscar un módulo"));
		$this->setTxtExplicacion("Debe salir y volver a entrar en la aplicación parar que los cambios tengan efecto");

		$this->setClase('permisos\\model\\entity\\ModuloInstalado');
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
			$aOperador='';
		}
		$oLista = new entity\GestorModuloInstalado();
		$Coleccion=$oLista->getModulosInstalados($aWhere,$aOperador);

		return $Coleccion;
	}
	
}