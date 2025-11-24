<?php

namespace src\ubis\infrastructure\repositories;

use core\ConfigGlobal;
use src\utils_database\domain\GenerateIdGlobal;

class PgCasaDlRepository extends PgCasaRepository
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cdc_dl');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_cdc_dl_id_auto_seq'::regclass)";
        return (int)$oDbl->query($sQuery)->fetchColumn();
    }

    /**
     * @throws \Exception
     */
    public function getNewIdUbi($id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }
}