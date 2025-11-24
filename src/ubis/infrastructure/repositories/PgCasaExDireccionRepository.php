<?php

namespace src\ubis\infrastructure\repositories;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_cdc_ex_dir
 */
class PgCasaExDireccionRepository extends PgRelacionUbiDireccionRepository
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