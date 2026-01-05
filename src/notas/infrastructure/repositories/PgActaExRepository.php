<?php

namespace src\notas\infrastructure\repositories;

use src\notas\domain\contracts\ActaExRepositoryInterface;


/**
 * Clase que adapta la tabla e_actas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
class PgActaExRepository extends PgActaRepository implements ActaExRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas_ex');
    }
}