<?php

namespace src\ubis\infrastructure\repositories;

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
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_cdc_dir');
    }

}