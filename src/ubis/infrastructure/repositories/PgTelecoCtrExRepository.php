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
class PgTelecoCtrExRepository extends PgTelecoUbiRepository
{

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('d_teleco_ctr_ex');
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_ctr_ex_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}