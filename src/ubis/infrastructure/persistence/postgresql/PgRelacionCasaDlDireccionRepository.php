<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_cdc_dl_dir
 */
class PgRelacionCasaDlDireccionRepository extends PgRelacionUbiDireccionRepository implements RelacionCasaDlDireccionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_cdc_dl_dir');
    }

}