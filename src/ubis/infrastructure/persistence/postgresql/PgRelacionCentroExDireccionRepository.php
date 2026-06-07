<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

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
        $oDbl = GlobalPdo::get('oDBR');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBR');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_ctr_ex_dir');
    }

}