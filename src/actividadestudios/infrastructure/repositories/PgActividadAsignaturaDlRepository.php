<?php

namespace src\actividadestudios\infrastructure\repositories;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_asignaturas_activ_all a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
class PgActividadAsignaturaDlRepository extends PgActividadAsignaturaRepository implements ActividadAsignaturaDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asignaturas_activ_dl');
    }
}