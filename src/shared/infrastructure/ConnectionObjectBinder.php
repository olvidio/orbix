<?php

namespace src\shared\infrastructure;

use PDO;
use RuntimeException;
use src\shared\domain\contracts\ConnectionObjectBinderInterface;

class ConnectionObjectBinder implements ConnectionObjectBinderInterface
{
    public function bindConnection(object $object, PDO $oDbl): object
    {
        if (!method_exists($object, 'setoDbl')) {
            throw new RuntimeException(sprintf('El objeto %s no soporta setoDbl()', get_class($object)));
        }

        $object->setoDbl($oDbl);

        return $object;
    }
}

