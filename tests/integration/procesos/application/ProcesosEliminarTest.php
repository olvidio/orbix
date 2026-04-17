<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ProcesosEliminar;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use Tests\factories\procesos\TareaProcesoFactory;
use Tests\myTest;

/**
 * Tests de integración para ProcesosEliminar.
 *
 * Cubre eliminación exitosa y el mensaje de error cuando el id no existe.
 */
class ProcesosEliminarTest extends myTest
{
    private TareaProcesoRepositoryInterface $repository;
    private TareaProcesoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $this->factory = new TareaProcesoFactory();
    }

    public function test_elimina_tarea_existente(): void
    {
        $oTareaProceso = $this->factory->createSimple();
        $id_item = $oTareaProceso->getId_item();
        $this->repository->Guardar($oTareaProceso);

        $this->assertNotNull($this->repository->findById($id_item));

        $msg = (new ProcesosEliminar())->execute(['id_item' => $id_item]);
        $this->assertSame('', $msg);
        $this->assertNull($this->repository->findById($id_item));
    }

    public function test_id_inexistente_devuelve_mensaje_error(): void
    {
        $msg = (new ProcesosEliminar())->execute(['id_item' => 99999999]);
        $this->assertSame(_('no se encuentra la tarea a borrar'), $msg);
    }
}
