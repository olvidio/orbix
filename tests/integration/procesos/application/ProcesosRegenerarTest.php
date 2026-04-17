<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ProcesosRegenerar;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use Tests\factories\procesos\TareaProcesoFactory;
use Tests\myTest;

/**
 * Tests de integración para ProcesosRegenerar.
 *
 * Verifica que la ejecución con un id_tipo_proceso que no tiene tareas
 * termina sin errores y devuelve la cadena vacía (comportamiento
 * declarado por el caso de uso).
 */
class ProcesosRegenerarTest extends myTest
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

    public function test_regenerar_proceso_sin_tareas_no_falla(): void
    {
        $msg = (new ProcesosRegenerar())->execute(['id_tipo_proceso' => 999999999]);
        $this->assertSame('', $msg);
    }

    public function test_regenerar_con_tareas_no_falla(): void
    {
        $id_tipo_proceso = 991500;

        $o = $this->factory->createSimple();
        $o->setId_tipo_proceso($id_tipo_proceso);
        $this->idsCreados[] = $o->getId_item();
        $this->repository->Guardar($o);

        $msg = (new ProcesosRegenerar())->execute(['id_tipo_proceso' => $id_tipo_proceso]);
        $this->assertSame('', $msg);
    }
}
