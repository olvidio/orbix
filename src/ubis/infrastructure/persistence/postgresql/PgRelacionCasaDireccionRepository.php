<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_cdc_dir
 */
class PgRelacionCasaDireccionRepository extends PgRelacionUbiDireccionRepository implements RelacionCasaDireccionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_cdc_dir');
    }

}