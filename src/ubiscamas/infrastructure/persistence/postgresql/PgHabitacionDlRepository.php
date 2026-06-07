<?php

namespace src\ubiscamas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;

/**
 * Clase que adapta la tabla du_habitaciones_dl a la interfaz del repositorio
 * Extiende de PgHabitacionRepository
 *
 * @package orbix
 * @subpackage ubiscamas
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/03/2026
 */
class PgHabitacionDlRepository extends PgHabitacionRepository implements HabitacionDlRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('du_habitaciones_dl');
    }
}
