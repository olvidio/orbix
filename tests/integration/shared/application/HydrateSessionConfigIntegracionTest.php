<?php

declare(strict_types=1);

namespace Tests\integration\shared\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\application\HydrateSessionConfig;
use Tests\myTest;

/** La configuración de la delegación se deja en `$_SESSION['oConfig']` a partir de la BD real. */
final class HydrateSessionConfigIntegracionTest extends myTest
{
    public function test_deja_un_snapshot_de_configuracion_en_sesion(): void
    {
        $anterior = $_SESSION['oConfig'] ?? null;
        unset($_SESSION['oConfig']);

        try {
            (new HydrateSessionConfig())->execute();

            $this->assertInstanceOf(ConfigSnapshot::class, $_SESSION['oConfig']);
        } finally {
            if ($anterior === null) {
                unset($_SESSION['oConfig']);
            } else {
                $_SESSION['oConfig'] = $anterior;
            }
        }
    }
}
