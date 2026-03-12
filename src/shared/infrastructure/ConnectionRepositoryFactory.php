<?php

namespace src\shared\infrastructure;

use PDO;
use RuntimeException;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;

class ConnectionRepositoryFactory implements ConnectionRepositoryFactoryInterface
{
    public function createWithConnection(string $repositoryId, PDO $oDbl, ?PDO $oDblSelect = null): object
    {
        $repository = $GLOBALS['container']->get($repositoryId);

        if (!is_object($repository)) {
            throw new RuntimeException(sprintf('El repositorio %s no es un objeto válido', $repositoryId));
        }
        // PHP-DI devuelve servicios shared por defecto. Clonamos para evitar
        // pisar la conexión cuando se usan repos de origen y destino a la vez.
        $repository = clone $repository;
        if (!method_exists($repository, 'setoDbl')) {
            throw new RuntimeException(sprintf('El repositorio %s no soporta setoDbl()', $repositoryId));
        }

        $repository->setoDbl($oDbl);
        if (method_exists($repository, 'setoDbl_select')) {
            $oDblSelect = $oDblSelect ?? $oDbl;
            $repository->setoDbl_select($oDblSelect);
        }

        return $repository;
    }
}
