<?php

namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;

class GestorPersonaNotaOtraRegionStgrDB extends GestorPersonaNotaDB
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    function __construct($esquema_region_stgr)
    {
        $this->esquema_region_stgr = $esquema_region_stgr;
        $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        $oConfigDB = new ConfigDB($db); //de la database sv/sf
        $config = $oConfigDB->getEsquema($esquema_region_stgr);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_otra_region_stgr');
    }
}
