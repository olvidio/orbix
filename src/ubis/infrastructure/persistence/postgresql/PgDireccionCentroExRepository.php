<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\PlanoOperationsTrait;
use src\utils_database\domain\GenerateIdGlobal;

/**
 * Clase que adapta la tabla u_dir_ctr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class PgDireccionCentroExRepository extends PgDireccionRepository implements DireccionCentroExRepositoryInterface
{
    use PlanoOperationsTrait;

    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBR');
        $oDbl_Select = GlobalPdo::get('oDBR');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_dir_ctr_ex');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_dir_ctr_ex_id_auto_seq'::regclass)";
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
    public function getNewIdDireccion(int $id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }

    protected function getPdoConnection(): \PDO
    {
        return $this->getoDbl();
    }
}