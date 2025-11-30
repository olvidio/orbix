<?php

namespace personas\model;

use core\DatosInfo;

/* No vale el underscore en el nombre */

class InfoLatin extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("nombres en latín"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este nombre?"));
        $this->setTxtBuscar(_("buscar en vernácula"));
        $this->setTxtExplicacion();

        $this->setClase('personas\\model\\entity\\NombreLatin');
        $this->setMetodoGestor('getNombresLatin');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nom');
            $aOperador = [];
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = new entity\GestorNombreLatin();
        $Coleccion = $oLista->getNombresLatin($aWhere, $aOperador);

        return $Coleccion;
    }
}