<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\TipoActivProcesoLstPosibles;
use Tests\myTest;

/**
 * Test smoke: `execute()` devuelve payload JSON como el backend
 * (`/src/procesos/tipo_activ_proceso_lst_posibles`); la mini-tabla HTML la pinta el frontend.
 */
class TipoActivProcesoLstPosiblesTest extends myTest
{
    public function test_devuelve_lista_estructurada_de_procesos_posibles(): void
    {
        $payload = (new TipoActivProcesoLstPosibles())->execute([
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
