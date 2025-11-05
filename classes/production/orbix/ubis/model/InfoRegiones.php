<?php

namespace ubis\model;


/* No vale el underscore en el nombre */

use core\DatosInfo;

class InfoRegiones extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("regiones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta región?"));
        $this->setTxtBuscar(_("buscar una región (sigla)"));
        $this->setTxtExplicacion();

        $this->setClase('ubis\\model\\entity\\Region');
        $this->setMetodoGestor('getRegiones');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'region');
            $aOperador = '';
        } else {
            $aWhere = array('region' => $this->k_buscar);
            $aOperador = array('region' => 'sin_acentos');
        }
        $oLista = new entity\GestorRegion();
        $Coleccion = $oLista->getRegiones($aWhere, $aOperador);

        return $Coleccion;
    }
}