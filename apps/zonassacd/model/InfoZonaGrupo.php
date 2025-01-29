<?php

namespace zonassacd\model;

/* No vale el underscore en el nombre */

use core\DatosInfo;

class InfoZonaGrupo extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("zonas geográficas (grupo)"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este grupo de zonas?"));
        $this->setTxtBuscar(_("grupo de zonas a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('zonassacd\\model\\entity\\ZonaGrupo');
        $this->setMetodoGestor('getZonasGrupo');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nombre_grupo');
            $aOperador = '';
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = new entity\GestorZonaGrupo();
        $Coleccion = $oLista->getZonasGrupo($aWhere, $aOperador);

        return $Coleccion;
    }
}