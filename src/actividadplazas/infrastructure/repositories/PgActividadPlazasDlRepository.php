<?php

namespace src\actividadplazas\infrastructure\repositories;

use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla da_plazas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgActividadPlazasDlRepository extends PgActividadPlazasRepository implements ActividadPlazasDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('da_plazas_dl');
    }

}