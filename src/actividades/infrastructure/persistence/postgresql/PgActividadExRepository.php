<?php

namespace src\actividades\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\utils_database\domain\GenerateIdGlobal;
use src\actividades\domain\entity\TiposActividades;


/**
 * Clase que adapta la tabla a_actividades_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
class PgActividadExRepository extends PgActividadAllRepository implements ActividadExRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(TiposActividades $tiposActividades)
    {
        parent::__construct($tiposActividades);
        $oDbl = GlobalPdo::get('oDBRC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBRC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_actividades_ex');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_actividades_ex_id_auto_seq'::regclass)";
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
    public function getNewIdActividad(int $id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }

}