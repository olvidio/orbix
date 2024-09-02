<?php
namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use Tests\config\MockConfigDB;

/**
 * Fitxer amb la Classe que accedeix a la taula e_notas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad e_notas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class PersonaNotaCertificado extends PersonaNota
{

    function __construct(string $nombre_schema, $mock = FALSE)
    {
        // Conectar con la tabla de la dl
        $db = (ConfigGlobal::mi_sfsv() === 1 )? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        if ($mock) {
            $oConfigDB = new MockConfigDB($db);
        }else {
            $oConfigDB = new ConfigDB($db); //de la database sv/sf
        }
        $config = $oConfigDB->getEsquema($nombre_schema);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }

}
