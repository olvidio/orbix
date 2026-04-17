<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ProcesosSelectData;
use Tests\myTest;

/**
 * Test de integración para el data reader ProcesosSelectData.
 *
 * Sólo comprueba forma del array devuelto (smoke test).
 */
class ProcesosSelectDataTest extends myTest
{
    public function test_execute_devuelve_array_con_tipos_proceso(): void
    {
        $data = ProcesosSelectData::execute();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('a_tipos_proceso', $data);
        $this->assertIsArray($data['a_tipos_proceso']);
    }
}
