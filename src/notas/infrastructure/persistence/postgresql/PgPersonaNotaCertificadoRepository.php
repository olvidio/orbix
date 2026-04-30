<?php
namespace src\notas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;
use src\notas\domain\contracts\PersonaNotaCertificadoRepositoryInterface;

class PgPersonaNotaCertificadoRepository extends PgPersonaNotaRepository implements PersonaNotaCertificadoRepositoryInterface
{

    public function __construct(?string $nombre_schema = null)
    {
        parent::__construct();
        if ($nombre_schema === null || $nombre_schema === '') {
            $nombre_schema = ConfigGlobal::mi_region_dl();
        }
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
