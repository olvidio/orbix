<?php

namespace src\cartaspresentacion\infrastructure\repositories;

use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionExRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla du_presentacion_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCartaPresentacionExRepository extends PgCartaPresentacionRepository implements CartaPresentacionExRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('du_presentacion_ex');
    }

}