<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\utils_database\domain\GenerateIdGlobal;

class PgCasaExRepository extends PgCasaRepository implements CasaExRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBRC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBRC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cdc_ex');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_cdc_ex_id_auto_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }

    /**
     * @throws \Exception
     */
    public function getNewIdUbi(int $id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }
}