<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ProcesosUpdate;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use Tests\factories\procesos\TareaProcesoFactory;
use Tests\myTest;

/**
 * Tests de integración para ProcesosUpdate.
 */
class ProcesosUpdateTest extends myTest
{
    private TareaProcesoRepositoryInterface $repository;
    private TareaProcesoFactory $factory;
    private int $id_item;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $this->factory = new TareaProcesoFactory();

        $oTareaProceso = $this->factory->createSimple();
        $this->id_item = $oTareaProceso->getId_item();
        $this->repository->Guardar($oTareaProceso);
    }

    public function tearDown(): void
    {
        $oTareaProceso = $this->repository->findById($this->id_item);
        if ($oTareaProceso !== null) {
            $this->repository->Eliminar($oTareaProceso);
        }
        parent::tearDown();
    }

    public function test_update_campos_basicos(): void
    {
        $input = [
            'id_item' => $this->id_item,
            'id_tipo_proceso' => 99,
            'status' => 3,
            'id_of_responsable' => 77,
            'id_fase' => 5,
            'id_tarea' => 2,
            'id_fase_previa' => [],
            'id_tarea_previa' => [],
            'mensaje_requisito' => [],
        ];

        $msg = DependencyResolver::get(ProcesosUpdate::class)->execute($input);
        $this->assertSame('', $msg);

        $oTareaProceso = $this->repository->findById($this->id_item);
        $this->assertNotNull($oTareaProceso);
        $this->assertSame(3, (int)$oTareaProceso->getStatus());
        $this->assertSame(77, (int)$oTareaProceso->getId_of_responsable());
        $this->assertSame(5, (int)$oTareaProceso->getId_fase());
        $this->assertSame(2, (int)$oTareaProceso->getId_tarea());
    }

    public function test_update_guarda_array_fases_previas_filtrando_vacias(): void
    {
        $input = [
            'id_item' => $this->id_item,
            'id_tipo_proceso' => 99,
            'status' => 2,
            'id_of_responsable' => 1,
            'id_fase' => 5,
            'id_tarea' => 0,
            'id_fase_previa' => [0 => '', 1 => '8'],
            'id_tarea_previa' => [0 => '', 1 => '3'],
            'mensaje_requisito' => [0 => '', 1 => 'requisito'],
        ];

        $msg = DependencyResolver::get(ProcesosUpdate::class)->execute($input);
        $this->assertSame('', $msg);

        $oTareaProceso = $this->repository->findById($this->id_item);
        $aPrevias = $oTareaProceso->getJson_fases_previas(true);

        $this->assertIsArray($aPrevias);
        $this->assertCount(1, $aPrevias);
        $this->assertSame('8', (string)$aPrevias[0]['id_fase']);
        $this->assertSame('3', (string)$aPrevias[0]['id_tarea']);
        $this->assertSame('requisito', $aPrevias[0]['mensaje']);
    }

    public function test_id_tarea_vacio_se_guarda_como_cero(): void
    {
        $input = [
            'id_item' => $this->id_item,
            'id_tipo_proceso' => 99,
            'status' => 2,
            'id_of_responsable' => 1,
            'id_fase' => 5,
            'id_tarea' => '',
            'id_fase_previa' => [],
            'id_tarea_previa' => [],
            'mensaje_requisito' => [],
        ];

        $msg = DependencyResolver::get(ProcesosUpdate::class)->execute($input);
        $this->assertSame('', $msg);

        $oTareaProceso = $this->repository->findById($this->id_item);
        $this->assertSame(0, (int)$oTareaProceso->getId_tarea());
    }
}
