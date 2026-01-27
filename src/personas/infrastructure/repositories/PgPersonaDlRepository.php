<?php

namespace src\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;

/**
 * Clase que adapta la tabla personas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PgPersonaDlRepository extends PgPersonaDlRepositoryBase implements PersonaDlRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('personas_dl');
    }

    /**
     * Crea una entidad PersonaDl desde un array de datos
     */
    protected function createEntityFromArray(array $aDatos): PersonaDl
    {
        return PersonaDl::fromArray($aDatos);
    }
}