<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\TipoActivProcesoLstPosibles;
use src\shared\infrastructure\DependencyResolver;
use Tests\myTest;

/**
 * Test smoke: execute() devuelve payload JSON de procesos posibles.
 */
class TipoActivProcesoLstPosiblesTest extends myTest
{
    public function test_devuelve_lista_estructurada_de_procesos_posibles(): void
    {
        $payload = DependencyResolver::get(TipoActivProcesoLstPosibles::class)->execute([
            'id_tipo_activ' => 0,
            'propio' => 't',
        ]);

        $this->assertIsArray($payload);
        $this->assertSame(0, $payload['id_tipo_activ']);
        $this->assertSame('t', $payload['propio']);
        $this->assertArrayHasKey('a_procesos', $payload);
        $this->assertIsArray($payload['a_procesos']);

        foreach ($payload['a_procesos'] as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('id_tipo_proceso', $row);
            $this->assertArrayHasKey('nom_proceso', $row);
            $this->assertIsInt($row['id_tipo_proceso']);
            $this->assertIsString($row['nom_proceso']);
        }
    }
}
