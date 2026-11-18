<?php

declare(strict_types=1);

namespace src\cambios\domain\contracts;

/**
 * Lanza el generador de la tabla de avisos en segundo plano y espera a que tenga tiempo de acabar.
 */
interface AvisosGenerarTablaLanzadorInterface
{
    public function lanzarYEsperar(): void;
}
