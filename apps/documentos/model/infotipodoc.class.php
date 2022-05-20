<?php
namespace documentos\model;
use core;

/* No vale el underscore en el nombre */
class InfoTipoDoc extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("tipos documento"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de documento?"));
		$this->setTxtBuscar(_("buscar un tipo de documento"));
		$this->setTxtExplicacion();
		
		$this->setClase('documentos\\model\\entity\\TipoDoc');
		$this->setMetodoGestor('getTiposDoc');
	}
	
	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'sigla');
			$aOperador='';
		} else {
			$aWhere=array('sigla'=> $this->k_buscar);
			$aOperador=array('sigla'=>'sin_acentos');
		}
		$oLista=new entity\GestorTipoDoc();
		
		return $oLista->getTiposDoc($aWhere,$aOperador);
	}
}