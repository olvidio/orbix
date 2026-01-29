<?php

namespace Tests\integration\actividades\infrastructure\repositories;

use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use Tests\myTest;
use Tests\factories\actividades\ActividadAllFactory;

class PgActividadDlRepositoryTest extends myTest
{
    private ActividadDlRepositoryInterface $repository;
    private ActividadAllFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $this->factory = new ActividadAllFactory();
    }

    public function test_guardar_nuevo_actividadAll()
    {
        // Crear instancia usando factory
        $oActividadAll = $this->factory->createSimple();
        $id = $oActividadAll->getId_activ();

        // Guardar
        $result = $this->repository->Guardar($oActividadAll);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadAllGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActividadAllGuardado);
        $this->assertEquals($id, $oActividadAllGuardado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oActividadAllGuardado);
    }

    public function test_actualizar_actividadAll_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadAll = $this->factory->createSimple();
        $id = $oActividadAll->getId_activ();
        $this->repository->Guardar($oActividadAll);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadAllUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadAllUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadAllActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActividadAllActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadAllActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadAll = $this->factory->createSimple();
        $id = $oActividadAll->getId_activ();
        $this->repository->Guardar($oActividadAll);

        // Buscar por ID
        $oActividadAllEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActividadAllEncontrado);
        $this->assertInstanceOf(ActividadAll::class, $oActividadAllEncontrado);
        $this->assertEquals($id, $oActividadAllEncontrado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oActividadAllEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadAll = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oActividadAll);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadAll = $this->factory->createSimple();
        $id = $oActividadAll->getId_activ();
        $this->repository->Guardar($oActividadAll);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id, $aDatos['id_activ']);

        // Limpiar
        $oActividadAllParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActividadAllParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadAll()
    {
        // Crear y guardar instancia usando factory
        $oActividadAll = $this->factory->createSimple();
        $id = $oActividadAll->getId_activ();
        $this->repository->Guardar($oActividadAll);

        // Verificar que existe
        $oActividadAllExiste = $this->repository->findById($id);
        $this->assertNotNull($oActividadAllExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadAllExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadAllEliminado = $this->repository->findById($id);
        $this->assertNull($oActividadAllEliminado);
    }

    public function test_get_ubis_sin_filtros()
    {
        $result = $this->repository->getUbis();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_actividades_de_tipo_sin_filtros()
    {
        $result = $this->repository->getArrayActividadesDeTipo();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_ids_with_key_fini_sin_filtros()
    {
        $result = $this->repository->getArrayIdsWithKeyFini();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_actividades_sin_filtros()
    {
        $result = $this->repository->getActividades();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
