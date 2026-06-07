<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;

/**
 * Clase que adapta la tabla d_teleco_cdc a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
class PgTelecoCdcDlRepository extends PgTelecoUbiRepository implements TelecoCdcDlRepositoryInterface
{

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_teleco_cdc_dl');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_cdc_dl_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}