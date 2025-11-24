<?php

namespace src\ubis\infrastructure\repositories;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_cdc_dl_dir
 */
class PgCasaDlDireccionRepository extends PgRelacionUbiDireccionRepository
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_cdc_dl_dir');
    }

}