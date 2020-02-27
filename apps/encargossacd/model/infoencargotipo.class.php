<?php
namespace encargossacd\model;
use core;
use encargossacd\model\entity\GestorEncargoTipo;

/* No vale el underscore en el nombre */
class InfoEncargoTipo extends core\datosInfo {
    
    public function __construct() {
        $this->setTxtTitulo(_("tipos de encargos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de encargo?"));
        $this->setTxtBuscar(_("buscar un tipo de encargo"));
        $this->setTxtExplicacion(_("solo los tipos 4000 y 7000 aparecen en el planning"));
        
        $this->setClase('encargossacd\\model\\entity\\EncargoTipo');
        $this->setMetodoGestor('getEncargoTipos');
    }
    
    public function getColeccion() {
        $aWhere = [];
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['tipo_enc']= $this->k_buscar;
            $aOperador['tipo_enc']='sin_acentos';
        }
        $aWhere['_ordre']='id_tipo_enc';
        $oLista=new GestorEncargoTipo();
        $Coleccion=$oLista->getEncargoTipos($aWhere,$aOperador);
        
        return $Coleccion;
    }
}