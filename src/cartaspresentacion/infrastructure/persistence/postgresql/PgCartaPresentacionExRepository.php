<?php

namespace src\cartaspresentacion\infrastructure\persistence\postgresql;

use src\cartaspresentacion\domain\contracts\CartaPresentacionExRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla du_presentacion_ex a la interfaz del repositorio
 */
class PgCartaPresentacionExRepository extends PgCartaPresentacionRepository implements CartaPresentacionExRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $this->setoDbl(GlobalPdo::get('oDBR'));
        $this->setNomTabla('du_presentacion_ex');
    }
}
