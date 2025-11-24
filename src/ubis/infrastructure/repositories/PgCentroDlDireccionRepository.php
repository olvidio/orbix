<?php

namespace src\ubis\infrastructure\repositories;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_ctr_dl_dir
 */
class PgCentroDlDireccionRepository extends PgRelacionUbiDireccionRepository
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('u_cross_ctr_dl_dir');
    }

}