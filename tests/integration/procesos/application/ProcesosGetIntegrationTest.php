<?php

namespace Tests\integration\procesos\application;

use frontend\procesos\support\ProcesosTreeHtml;
use src\procesos\application\ProcesosGet;
use src\procesos\application\ProcesosGetListado;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use Tests\factories\procesos\TareaProcesoFactory;
use Tests\myTest;

/**
 * Tests de integración para ProcesosGet y ProcesosGetListado.
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

    public function test_get_sin_tareas_devuelve_arbol_vacio(): void
    {
        $payload = DependencyResolver::get(ProcesosGet::class)->execute(['id_tipo_proceso' => 999999999]);
        $this->assertSame(['aPadres' => []], $payload);
        $this->assertSame('', ProcesosTreeHtml::dibujarTree($payload['aPadres']));
    }

    public function test_get_listado_sin_tareas_devuelve_filas_vacias(): void
    {
        $payload = DependencyResolver::get(ProcesosGetListado::class)->execute(['id_tipo_proceso' => 999999999]);
        $this->assertSame(['a_rows' => []], $payload);
    }
}
