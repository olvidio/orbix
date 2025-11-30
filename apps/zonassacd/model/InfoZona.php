<?php

namespace zonassacd\model;

use core\DatosInfo;

/* No vale el underscore en el nombre */

class InfoZona extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("zonas de misas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta zona?"));
        $this->setTxtBuscar(_("zona a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('zonassacd\\model\\entity\\Zona');
        $this->setMetodoGestor('getZonas');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'orden');
            $aOperador = [];
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = new entity\GestorZona();
        $Coleccion = $oLista->getZonas($aWhere, $aOperador);

        return $Coleccion;
    }
}