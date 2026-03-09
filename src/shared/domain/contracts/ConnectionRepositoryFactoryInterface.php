<?php

namespace src\shared\domain\contracts;

use PDO;

interface ConnectionRepositoryFactoryInterface
{
    /**
     * Resuelve un repositorio del contenedor y lo liga a una conexión específica.
     */
    public function createWithConnection(string $repositoryId, PDO $oDbl, ?PDO $oDblSelect = null): object;
}

