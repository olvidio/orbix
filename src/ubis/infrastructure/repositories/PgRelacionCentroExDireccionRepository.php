<?php

namespace src\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_ctr_ex_dir
 */
class PgRelacionCentroExDireccionRepository extends PgRelacionUbiDireccionRepository implements RelacionCentroExDireccionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBR_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_ctr_ex_dir');
    }

}