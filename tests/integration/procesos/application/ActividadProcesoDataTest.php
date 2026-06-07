<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ActividadProcesoData;
use src\shared\infrastructure\DependencyResolver;
use Tests\myTest;

/**
 * Test de integración para el data reader ActividadProcesoData.
 *
 * Verifica forma del array devuelto y que un id inexistente resulta en
 * nom_activ vacío.
 */
class ActividadProcesoDataTest extends myTest
{
    public function test_id_inexistente_devuelve_nombre_vacio(): void
    {
        $data = DependencyResolver::get(ActividadProcesoData::class)->execute(999999999);

        $this->assertIsArray($data);
        $this->assertSame(999999999, $data['id_activ']);
        $this->assertSame('', $data['nom_activ']);
    }
}
