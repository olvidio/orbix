<?php

namespace encargossacd\model;

use core\DatosInfo;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;

/* No vale el underscore en el nombre */

class InfoEncargoTipo extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de encargos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de encargo?"));
        $this->setTxtBuscar(_("buscar un tipo de encargo"));
        $this->setTxtExplicacion(_("solo los tipos 4000 y 7000 aparecen en el planning"));

        $this->setClase('encargossacd\\model\\entity\\EncargoTipo');
        $this->setMetodoGestor('getEncargoTipos');
    }

    public function getColeccion()
    {
        $aWhere = [];
        $aOperador = [];
        $EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['tipo_enc'] = $this->k_buscar;
            $aOperador['tipo_enc'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'id_tipo_enc';
        $Coleccion = $EncargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

        return $Coleccion;
    }
}