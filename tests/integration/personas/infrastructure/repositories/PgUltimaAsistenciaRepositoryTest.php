<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\personas\domain\entity\UltimaAsistencia;
use Tests\myTest;
use Tests\factories\personas\UltimaAsistenciaFactory;

class PgUltimaAsistenciaRepositoryTest extends myTest
{
    private UltimaAsistenciaRepositoryInterface $repository;
    private UltimaAsistenciaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(UltimaAsistenciaRepositoryInterface::class);
        $this->factory = new UltimaAsistenciaFactory();
    }

    public function test_guardar_nuevo_ultimaAsistencia()
    {
        // Crear instancia usando factory
        $oUltimaAsistencia = $this->factory->createSimple();
        $id = $oUltimaAsistencia->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oUltimaAsistencia);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oUltimaAsistenciaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oUltimaAsistenciaGuardado);
        $this->assertEquals($id, $oUltimaAsistenciaGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oUltimaAsistenciaGuardado);
    }

    public function test_actualizar_ultimaAsistencia_existente()
    {
        // Crear y guardar instancia usando factory
        $oUltimaAsistencia = $this->factory->createSimple();
        $id = $oUltimaAsistencia->getId_item();
        $this->repository->Guardar($oUltimaAsistencia);

        // Crear otra instancia con datos diferentes para actualizar
        $oUltimaAsistenciaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oUltimaAsistenciaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oUltimaAsistenciaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oUltimaAsistenciaActualizado);

        // Limpiar
        $this->repository->Eliminar($oUltimaAsistenciaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oUltimaAsistencia = $this->factory->createSimple();
        $id = $oUltimaAsistencia->getId_item();
        $this->repository->Guardar($oUltimaAsistencia);

        // Buscar por ID
        $oUltimaAsistenciaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oUltimaAsistenciaEncontrado);
        $this->assertInstanceOf(UltimaAsistencia::class, $oUltimaAsistenciaEncontrado);
        $this->assertEquals($id, $oUltimaAsistenciaEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oUltimaAsistenciaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oUltimaAsistencia = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oUltimaAsistencia);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oUltimaAsistencia = $this->factory->createSimple();
        $id = $oUltimaAsistencia->getId_item();
        $this->repository->Guardar($oUltimaAsistencia);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oUltimaAsistenciaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oUltimaAsistenciaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_ultimaAsistencia()
    {
        // Crear y guardar instancia usando factory
        $oUltimaAsistencia = $this->factory->createSimple();
        $id = $oUltimaAsistencia->getId_item();
        $this->repository->Guardar($oUltimaAsistencia);

        // Verificar que existe
        $oUltimaAsistenciaExiste = $this->repository->findById($id);
        $this->assertNotNull($oUltimaAsistenciaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oUltimaAsistenciaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oUltimaAsistenciaEliminado = $this->repository->findById($id);
        $this->assertNull($oUltimaAsistenciaEliminado);
    }

    public function test_get_ultimas_asistencias_sin_filtros()
    {
        $result = $this->repository->getUltimasAsistencias();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
