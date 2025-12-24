<?php

namespace src\actividades\infrastructure\repositories;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\utils_database\domain\GenerateIdGlobal;


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

    public function __construct(TipoTelecoRepositoryInterface $tipoTelecoRepository)
    {
        parent::__construct($tipoTelecoRepository);
        $oDbl = $GLOBALS['oDBRC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBRC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_actividades_ex');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_actividades_ex_id_auto_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
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