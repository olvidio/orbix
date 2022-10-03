<?php

namespace devel\model;

use core;

/* No vale el underscore en el nombre */

class InfoApps extends core\datosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("aplicaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta aplicación?"));
        $this->setTxtBuscar(_("buscar una aplicación"));
        $this->setTxtExplicacion();

        $this->setClase('devel\\model\\entity\\App');
        $this->setMetodoGestor('getApps');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nom');
            $aOperador = '';
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = new entity\GestorApp();
        $Coleccion = $oLista->getApps($aWhere, $aOperador);

        return $Coleccion;
    }
}