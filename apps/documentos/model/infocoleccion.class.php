<?php
namespace documentos\model;
use core;

/* No vale el underscore en el nombre */
class InfoColeccion extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("colecciones"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar esta colección?"));
		$this->setTxtBuscar(_("buscar una colección"));
		$this->setTxtExplicacion();
		
		$this->setClase('documentos\\model\\entity\\Coleccion');
		$this->setMetodoGestor('getColecciones');
	}
	
	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'nom_coleccion');
			$aOperador='';
		} else {
			$aWhere=array('nom_coleccion'=> $this->k_buscar);
			$aOperador=array('nom_coleccion'=>'sin_acentos');
		}
		$oLista=new entity\GestorColeccion();
		
		return $oLista->getColecciones($aWhere,$aOperador);
	}
}