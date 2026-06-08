<?php

declare(strict_types=1);

namespace src\shared\application;

use src\configuracion\application\ObtenerConfigSnapshot;
use src\shared\infrastructure\DependencyResolver;

/**
 * Carga la configuración de delegación en `$_SESSION['oConfig']`.
 */
final class HydrateSessionConfig
{
    public function execute(): void
    {
        $_SESSION['oConfig'] = DependencyResolver::get(ObtenerConfigSnapshot::class)->execute();
    }
}
