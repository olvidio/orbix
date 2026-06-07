<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ProcesosSelectData;
use src\shared\infrastructure\DependencyResolver;
use Tests\myTest;

/**
 * Test de integración para el data reader ProcesosSelectData.
 */
class ProcesosSelectDataTest extends myTest
{
    public function test_execute_devuelve_array_con_tipos_proceso(): void
    {
        $data = DependencyResolver::get(ProcesosSelectData::class)->execute();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('a_tipos_proceso', $data);
        $this->assertIsArray($data['a_tipos_proceso']);
    }
}
