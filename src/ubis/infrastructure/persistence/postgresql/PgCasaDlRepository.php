<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\utils_database\domain\GenerateIdGlobal;

class PgCasaDlRepository extends PgCasaRepository implements CasaDlRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cdc_dl');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_cdc_dl_id_auto_seq'::regclass)";
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