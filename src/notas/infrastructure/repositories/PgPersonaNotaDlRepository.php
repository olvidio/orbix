<?php

namespace src\notas\infrastructure\repositories;

use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;

/**
 * Clase que adapta la tabla e_actas_tribunal_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
class PgPersonaNotaDlRepository extends PgPersonaNotaRepository implements PersonaNotaDlRepositoryInterface
{

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }

}