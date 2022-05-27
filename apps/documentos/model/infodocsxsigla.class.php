<?php
namespace documentos\model;
use core;

/* No vale el underscore en el nombre */
class InfoDocsxSigla extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("documentos"));
		$this->setTxtEliminar(_("¿Está seguro que desea eliminar este documento?"));
		$this->setTxtBuscar(_("buscar un centro"));
		$this->setTxtExplicacion();
		
		$this->setClase('documentos\\model\\entity\\Documento');
		$this->setMetodoGestor('getDocumentos');
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
		$oLista=new entity\GestorDocumento();
		
		return $oLista->getDocumentos($aWhere,$aOperador);
	}
}