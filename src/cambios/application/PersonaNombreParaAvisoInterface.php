<?php

declare(strict_types=1);

namespace src\cambios\application;

/**
 * Resuelve id_nom → nombre legible para textos de aviso.
 */
interface PersonaNombreParaAvisoInterface
{
    public function resolve(int $id_nom): ?string;
}
