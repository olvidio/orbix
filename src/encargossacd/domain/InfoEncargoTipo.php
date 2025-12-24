<?php

namespace src\encargossacd\domain;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoEncargoTipo extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de encargos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de encargo?"));
        $this->setTxtBuscar(_("buscar un tipo de encargo"));
        $this->setTxtExplicacion(_("solo los tipos 4000 y 7000 aparecen en el planning"));

        $this->setClase('src\\encargossacd\\domain\\entity\\EncargoTipo');
        $this->setMetodoGestor('getEncargoTipos');

        $this->setRepositoryInterface(EncargoTipoRepositoryInterface::class);
    }

    public function getColeccion()
    {
        $aWhere = [];
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['tipo_enc'] = $this->k_buscar;
            $aOperador['tipo_enc'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'id_tipo_enc';

        $oLista = $GLOBALS['container']->get($this->getRepositoryInterface());
        $Coleccion = $oLista->getEncargoTipos($aWhere, $aOperador);

        return $Coleccion;
    }
}