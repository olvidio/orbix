<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;

/**
 * Clase que adapta la tabla d_teleco_cdc a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
class PgTelecoCtrExRepository extends PgTelecoUbiRepository implements TelecoCtrExRepositoryInterface
{

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBR');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('d_teleco_ctr_ex');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_ctr_ex_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}