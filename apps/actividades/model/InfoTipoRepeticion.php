<?php

namespace actividades\model;

use core;

/* No vale el underscore en el nombre */

class InfoTipoRepeticion extends core\datosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipo de repetición de actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de repetición?"));
        $this->setTxtBuscar(_("buscar un tipo de repetición"));
        $this->setTxtExplicacion();

        $this->setClase('actividades\\model\\entity\\Repeticion');
        $this->setMetodoGestor('getRepeticiones');
    }

    public function getColeccion()
    {
        $aWhere = [];
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['repeticion'] = $this->k_buscar;
            $aOperador['repeticion'] = $this->k_buscar;
            $aOperador['repeticion'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'serie';
        $oLista = new entity\GestorRepeticion();
        $Coleccion = $oLista->getRepeticiones($aWhere, $aOperador);

        return $Coleccion;
    }
}