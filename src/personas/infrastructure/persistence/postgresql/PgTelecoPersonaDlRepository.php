<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;


/**
 * Clase que adapta la tabla d_teleco_personas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/12/2025
 */
class PgTelecoPersonaDlRepository extends PgTelecoPersonaRepository implements TelecoPersonaDlRepositoryInterface
{
    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setNomTabla('d_teleco_personas_dl');
    }


    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_personas_dl_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }

}