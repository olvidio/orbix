<?php

namespace src\ubis\infrastructure\repositories;


/**
 * Clase que adapta la tabla d_teleco_cdc a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
class PgTelecoCdcDlRepository extends PgTelecoUbiRepository
{

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_teleco_cdc_dl');
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_cdc_dl_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}