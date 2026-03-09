<?php

namespace src\personas\domain\contracts;

use PDO;

interface PersonaDlRepositoryFactoryInterface
{
    /**
     * Crea un repositorio con la conexión por defecto del módulo.
     */
    public function create(): PersonaDlRepositoryInterface;

    /**
     * Crea un repositorio ligado a una conexión específica.
     */
    public function createWithConnection(PDO $oDbl): PersonaDlRepositoryInterface;
}
