<?php

namespace notas\model\entity;

class GestorPersonaNotaDlDB extends GestorPersonaNotaDB
{

    function __construct()
    {
        /*
        $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        $oConfigDB = new ConfigDB($db); //de la database sv/sf
        $config = $oConfigDB->getEsquema(ConfigGlobal::mi_region_dl());
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();
        */
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }
}
