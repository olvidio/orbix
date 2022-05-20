<?php
namespace documentos\model;
use core;

/* No vale el underscore en el nombre */
class InfoUbiDoc extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("centros"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este centro?"));
		$this->setTxtBuscar(_("buscar un centro"));
		$this->setTxtExplicacion();
		
		$this->setClase('documentos\\model\\entity\\UbiDoc');
		$this->setMetodoGestor('getUbiDocs');
	}
	
	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array('_ordre'=>'nom_ubi');
			$aOperador='';
		} else {
			$aWhere=array('nom_ubi'=> $this->k_buscar);
			$aOperador=array('nom_ubi'=>'sin_acentos');
		}
		$oLista=new entity\GestorUbiDoc();
		
		return $oLista->getUbiDocs($aWhere,$aOperador);
	}
}