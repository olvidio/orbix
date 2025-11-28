<?php

namespace src\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_cdc_ex_dir
 */
class PgRelacionCasaExDireccionRepository extends PgRelacionUbiDireccionRepository implements RelacionCasaExDireccionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBRC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBRC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_cdc_ex_dir');
    }

}