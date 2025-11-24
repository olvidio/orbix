<?php

namespace src\ubis\infrastructure\repositories;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_ctr_ex_dir
 */
class PgCentroExDireccionRepository extends PgRelacionUbiDireccionRepository
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