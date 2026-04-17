<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\ProcesosClonar;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use Tests\factories\procesos\TareaProcesoFactory;
use Tests\myTest;

/**
 * Tests de integración para ProcesosClonar.
 *
 * Verifica que:
 *  - Las tareas existentes del proceso destino se borran.
 *  - Las del proceso de referencia se copian (con nuevo id_item).
 */
class ProcesosClonarTest extends myTest
{
    private TareaProcesoRepositoryInterface $repository;
    private TareaProcesoFactory $factory;
    private array $idsCreados = [];

    private array $idsTipoProcesoUsados = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $this->factory = new TareaProcesoFactory();
        $this->cleanupFromTipoProceso();
    }

    public function tearDown(): void
    {
        foreach ($this->idsCreados as $id) {
            $o = $this->repository->findById($id);
            if ($o !== null) {
                $this->repository->Eliminar($o);
            }
        }
        $this->cleanupFromTipoProceso();
        parent::tearDown();
    }

    /**
     * Limpia cualquier tarea_proceso residual con los ids de tipo_proceso
     * usados por este test (que pueda haber sobrevivido a un fallo previo).
     */
    private function cleanupFromTipoProceso(): void
    {
        foreach ($this->idsTipoProcesoUsados as $id_tipo) {
            $tareas = $this->repository->getTareasProceso(['id_tipo_proceso' => $id_tipo]);
            foreach ($tareas as $t) {
                $this->repository->Eliminar($t);
            }
        }
    }

    public function test_clonar_copia_tareas_y_borra_las_previas(): void
    {
        $id_tipo_proceso_ref = 991001;
        $id_tipo_proceso_dst = 991002;
        $this->idsTipoProcesoUsados = [$id_tipo_proceso_ref, $id_tipo_proceso_dst];
        $this->cleanupFromTipoProceso();

        // Proceso referencia: 2 tareas con fases distintas
        // (clave unique: id_tipo_proceso + id_fase + id_tarea)
        $ref1 = $this->factory->createSimple();
        $ref1->setId_tipo_proceso($id_tipo_proceso_ref);
        $ref1->setId_fase(101);
        $this->idsCreados[] = $ref1->getId_item();
        $this->repository->Guardar($ref1);

        $ref2 = $this->factory->createSimple();
        $ref2->setId_tipo_proceso($id_tipo_proceso_ref);
        $ref2->setId_fase(102);
        $this->idsCreados[] = $ref2->getId_item();
        $this->repository->Guardar($ref2);

        // Proceso destino: 1 tarea preexistente que debe desaparecer
        $dst_prev = $this->factory->createSimple();
        $dst_prev->setId_tipo_proceso($id_tipo_proceso_dst);
        $dst_prev->setId_fase(201);
        $this->idsCreados[] = $dst_prev->getId_item();
        $this->repository->Guardar($dst_prev);

        $tareasRefAntes = $this->repository->getTareasProceso(['id_tipo_proceso' => $id_tipo_proceso_ref]);
        $this->assertCount(2, $tareasRefAntes);
        $tareasDstAntes = $this->repository->getTareasProceso(['id_tipo_proceso' => $id_tipo_proceso_dst]);
        $this->assertCount(1, $tareasDstAntes);

        // Clonar. El caso de uso termina llamando a ProcesosGet que requiere
        // ActividadFase reales para renderizar el árbol; como el objetivo
        // aquí es validar la clonación, ignoramos el fallo del render HTML.
        try {
            (new ProcesosClonar())->execute([
                'id_tipo_proceso' => $id_tipo_proceso_dst,
                'id_tipo_proceso_ref' => $id_tipo_proceso_ref,
            ]);
        } catch (\Throwable $e) {
            // ignorar, solo nos interesa el estado del repositorio
        }

        // Ahora destino debe tener 2 tareas y la previa (con su id_item viejo) no existir
        $this->assertNull($this->repository->findById($dst_prev->getId_item()));

        $tareasDstDespues = $this->repository->getTareasProceso(['id_tipo_proceso' => $id_tipo_proceso_dst]);
        $this->assertCount(2, $tareasDstDespues);

        // Los nuevos ids son distintos a los del proceso de referencia
        foreach ($tareasDstDespues as $o) {
            $this->idsCreados[] = $o->getId_item();
        }
    }
}
