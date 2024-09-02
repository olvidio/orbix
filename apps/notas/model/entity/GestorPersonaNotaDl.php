<?php

namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use Tests\config\MockConfigDB;

/**
 * GestorPersonaNotaDl
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaNotaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorPersonaNotaDl extends GestorPersonaNota
{

    function __construct(bool $mock = FALSE, $a_id = '')
    {
        $this->mock = $mock;
        $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        if ($mock) {
            $oConfigDB = new MockConfigDB($db);
        } else {
            $oConfigDB = new ConfigDB($db); //de la database sv/sf
        }
        $config = $oConfigDB->getEsquema(ConfigGlobal::mi_region_dl());
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }
}
