<?php

namespace src\personas\infrastructure\repositories;

use PDO;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;

/**
 * Factoría transicional para evitar que consumidores llamen setoDbl()
 * directamente sobre el repositorio.
 */
class PersonaDlRepositoryFactory implements PersonaDlRepositoryFactoryInterface
{
    public function create(): PersonaDlRepositoryInterface
    {
        return new PgPersonaDlRepository();
    }

    public function createWithConnection(PDO $oDbl): PersonaDlRepositoryInterface
    {
        $repository = new PgPersonaDlRepository();
        $repository->setoDbl($oDbl);

        return $repository;
    }
}
