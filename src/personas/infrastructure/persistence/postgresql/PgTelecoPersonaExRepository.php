<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\TelecoPersonaExRepositoryInterface;
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
class PgTelecoPersonaExRepository extends PgTelecoPersonaRepository implements TelecoPersonaExRepositoryInterface
{
    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBR'));
        $this->setNomTabla('d_teleco_personas_ex');
    }


    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_personas_ex_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }

}