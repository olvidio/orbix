<?php
namespace documentos\model;
use core;

/* No vale el underscore en el nombre */
class InfoLugar extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("lugares"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este lugar?"));
		$this->setTxtBuscar(_("buscar un lugar"));
		$this->setTxtExplicacion();
		
		$this->setClase('documentos\\model\\entity\\Lugar');
		$this->setMetodoGestor('getLugares');
	}
	
	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'nom_lugar');
			$aOperador='';
		} else {
			$aWhere=array('nom_lugar'=> $this->k_buscar);
			$aOperador=array('nom_lugar'=>'sin_acentos');
		}
		$oLista=new entity\GestorLugar();
		
		return $oLista->getLugares($aWhere,$aOperador);
	}
}