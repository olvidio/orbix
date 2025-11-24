<?php

namespace src\ubis\infrastructure\repositories;


use core\ConfigGlobal;
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
class PgDireccionCentroExRepository extends PgDireccionRepository
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBR'];
        $oDbl_Select = $GLOBALS['oDBR_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_dir_ctr_ex');
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_dir_ctr_ex_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    /**
     * @throws \Exception
     */
    public function getNewIdDireccion($id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }
}