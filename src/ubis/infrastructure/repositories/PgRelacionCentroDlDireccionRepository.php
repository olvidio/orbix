<?php

namespace src\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_ctr_dl_dir
 */
class PgRelacionCentroDlDireccionRepository extends PgRelacionUbiDireccionRepository implements RelacionCentroDlDireccionRepositoryInterface
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