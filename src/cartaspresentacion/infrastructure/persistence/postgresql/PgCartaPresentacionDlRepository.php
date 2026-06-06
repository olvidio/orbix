<?php

namespace src\cartaspresentacion\infrastructure\persistence\postgresql;

use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla du_presentacion_dl a la interfaz del repositorio
 */
class PgCartaPresentacionDlRepository extends PgCartaPresentacionRepository implements CartaPresentacionDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setNomTabla('du_presentacion_dl');
    }
}
