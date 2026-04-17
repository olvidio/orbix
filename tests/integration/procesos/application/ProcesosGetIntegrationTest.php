<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ProcesosGet;
use src\procesos\application\ProcesosGetListado;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use Tests\factories\procesos\TareaProcesoFactory;
use Tests\myTest;

/**
 * Tests de integración para los data readers HTML:
 *  - ProcesosGet (árbol de fases)
 *  - ProcesosGetListado (tabla de fases)
 *
 * Smoke tests, no queremos validar todo el HTML, sólo que se construye
 * sin errores y con los datos creados.
 */
class ProcesosGetIntegrationTest extends myTest
{
    private TareaProcesoRepositoryInterface $repository;
    private TareaProcesoFactory $factory;
    private array $idsCreados = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $this->factory = new TareaProcesoFactory();
    }

    public function tearDown(): void
    {
        foreach ($this->idsCreados as $id) {
            $o = $this->repository->findById($id);
            if ($o !== null) {
                $this->repository->Eliminar($o);
            }
        }
        parent::tearDown();
    }

    public function test_get_sin_tareas_devuelve_cadena_vacia(): void
    {
        $html = (new ProcesosGet())->execute(['id_tipo_proceso' => 999999999]);
        $this->assertSame('', $html);
    }

    public function test_get_listado_sin_tareas_devuelve_tabla_vacia(): void
    {
        $html = (new ProcesosGetListado())->execute(['id_tipo_proceso' => 999999999]);
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('</table>', $html);
    }
}
