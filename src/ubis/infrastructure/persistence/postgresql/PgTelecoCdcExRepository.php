<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;

/**
 * Clase que adapta la tabla d_teleco_cdc a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
class PgTelecoCdcExRepository extends PgTelecoUbiRepository implements TelecoCdcExRepositoryInterface
{

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBRC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBRC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_teleco_cdc_ex');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_cdc_ex_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}