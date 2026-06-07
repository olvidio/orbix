<?php

namespace src\dbextern\application\support;

use RuntimeException;
use src\dbextern\domain\SincroDB;
use src\shared\infrastructure\DependencyResolver;

final class SincroDBFactory
{
    public function create(): SincroDB
    {
        $sincro = DependencyResolver::make(SincroDB::class);
        if (!$sincro instanceof SincroDB) {
            throw new RuntimeException('No se pudo crear SincroDB');
        }

        return $sincro;
    }
}
