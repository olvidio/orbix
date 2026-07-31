<?php

namespace src\notas\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;

/**
 * Adaptador de `e_notas_dl`.
 *
 * Sin argumento: PDO de sesión (`GlobalPdo::oDB`).
 * Con `$nombre_schema` (p. ej. `H-dlpv`): conexión a ese esquema (mapa acta → DL).
 */
class PgPersonaNotaDlRepository extends PgPersonaNotaRepository implements PersonaNotaDlRepositoryInterface
{
    public function __construct(?string $nombre_schema = null)
    {
        parent::__construct();
        if ($nombre_schema === null || $nombre_schema === '') {
            $oDbl = GlobalPdo::get('oDB');
        } else {
            $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
            $oConfigDB = new ConfigDB($db);
            $config = $oConfigDB->getEsquema($nombre_schema);
            $oDbl = (new DBConnection($config))->getPDO();
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }
}
