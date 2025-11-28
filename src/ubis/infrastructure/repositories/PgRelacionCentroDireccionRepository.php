<?php

namespace src\ubis\infrastructure\repositories;

use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\entity\Direccion;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_ctr_dir
 */
class PgRelacionCentroDireccionRepository extends PgRelacionUbiDireccionRepository implements RelacionCentroDireccionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('u_cross_ctr_dir');
    }

}