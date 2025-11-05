<?php
namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;

class PersonaNotaCertificadoDB extends PersonaNotaDB
{

    public function __construct(string $nombre_schema)
    {
        // Conectar con la tabla de la dl
        $db = (ConfigGlobal::mi_sfsv() === 1 )? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        $oConfigDB = new ConfigDB($db); //de la database sv/sf
        $config = $oConfigDB->getEsquema($nombre_schema);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }

}
