<?php
namespace ubis\model;
use core;

/* No vale el underscore en el nombre */
class InfoDelegaciones extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("delegaciones"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esta delegación?"));
		$this->setTxtBuscar(_("buscar una delegación (sigla)"));
		$this->setTxtExplicacion();

		$this->setClase('ubis\\model\\entity\\Delegacion');
		$this->setMetodoGestor('getDelegaciones');
	}

	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'region');
			$aOperador='';
		} else {
			$aWhere=array('dl'=> $this->k_buscar);
			$aOperador=array('dl'=>'sin_acentos');
		}
		$oLista=new entity\GestorDelegacion();
		$Coleccion=$oLista->getDelegaciones($aWhere,$aOperador);

		return $Coleccion;
	}
}