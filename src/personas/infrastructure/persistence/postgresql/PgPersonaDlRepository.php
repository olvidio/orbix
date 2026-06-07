<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\infrastructure\GlobalPdo;

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
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setNomTabla('personas_dl');
    }

    /**
     * Crea una entidad PersonaDl desde un array de datos
     */
    /** @param array<string, mixed> $aDatos */
    protected function createEntityFromArray(array $aDatos): PersonaDl
    {
        return PersonaDl::fromArray($aDatos);
    }

    public function findById(int $id_nom): ?PersonaDl
    {
        $aDatos = $this->datosById($id_nom);
        if ($aDatos === false) {
            return null;
        }
        return PersonaDl::fromArray($aDatos);
    }

}
