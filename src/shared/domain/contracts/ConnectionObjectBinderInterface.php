<?php

namespace src\shared\domain\contracts;

use PDO;

interface ConnectionObjectBinderInterface
{
    /**
     * Asigna una conexión a un objeto legacy que expone setoDbl().
     */
    public function bindConnection(object $object, PDO $oDbl): object;
}

