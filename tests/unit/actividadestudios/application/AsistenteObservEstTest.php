<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\AsistenteObservEst;

/**
 * El flujo completo depende de {@see AsistenteActividadService::getRepoAsistente}
 * (persona + actividad en global). Solo se cubren validaciones de entrada.
 */
final class AsistenteObservEstTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_faltan_ids_devuelve_mensaje(): void
    {
        $GLOBALS['container'] = new class {
            public function get(string $id): never
            {
                throw new \RuntimeException('no container: ' . $id);
            }
        };

        $msg = AsistenteObservEst::execute(['id_activ' => 0, 'id_nom' => 5]);
        $this->assertNotSame('', $msg);

        $msg2 = AsistenteObservEst::execute(['id_activ' => 9, 'id_nom' => 0, 'id_pau' => 0]);
        $this->assertNotSame('', $msg2);
    }
}
