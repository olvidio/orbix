<?php

namespace Tests\integration\cambios\infrastructure\repositories;

use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\entity\CambioAnotado;
use Tests\myTest;
use Tests\factories\cambios\CambioAnotadoFactory;

class PgCambioAnotadoRepositoryTest extends myTest
{
    private CambioAnotadoRepositoryInterface $repository;
    private CambioAnotadoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CambioAnotadoRepositoryInterface::class);
        $this->repository->setTabla("sv");
        $this->factory = new CambioAnotadoFactory();
    }

    public function test_guardar_nuevo_cambioAnotado()
    {
        // Crear instancia usando factory
        $oCambioAnotado = $this->factory->createSimple();
        $id = $oCambioAnotado->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oCambioAnotado);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCambioAnotadoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCambioAnotadoGuardado);
        $this->assertEquals($id, $oCambioAnotadoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCambioAnotadoGuardado);
    }

    public function test_actualizar_cambioAnotado_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioAnotado = $this->factory->createSimple();
        $id = $oCambioAnotado->getId_item();
        $this->repository->Guardar($oCambioAnotado);

        // Crear otra instancia con datos diferentes para actualizar
        $oCambioAnotadoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCambioAnotadoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCambioAnotadoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCambioAnotadoActualizado);

        // Limpiar
        $this->repository->Eliminar($oCambioAnotadoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioAnotado = $this->factory->createSimple();
        $id = $oCambioAnotado->getId_item();
        $this->repository->Guardar($oCambioAnotado);

        // Buscar por ID
        $oCambioAnotadoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCambioAnotadoEncontrado);
        $this->assertInstanceOf(CambioAnotado::class, $oCambioAnotadoEncontrado);
        $this->assertEquals($id, $oCambioAnotadoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCambioAnotadoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCambioAnotado = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCambioAnotado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioAnotado = $this->factory->createSimple();
        $id = $oCambioAnotado->getId_item();
        $this->repository->Guardar($oCambioAnotado);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oCambioAnotadoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCambioAnotadoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_cambioAnotado()
    {
        // Crear y guardar instancia usando factory
        $oCambioAnotado = $this->factory->createSimple();
        $id = $oCambioAnotado->getId_item();
        $this->repository->Guardar($oCambioAnotado);

        // Verificar que existe
        $oCambioAnotadoExiste = $this->repository->findById($id);
        $this->assertNotNull($oCambioAnotadoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCambioAnotadoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCambioAnotadoEliminado = $this->repository->findById($id);
        $this->assertNull($oCambioAnotadoEliminado);
    }

    public function test_get_cambios_anotados_sin_filtros()
    {
        $result = $this->repository->getCambiosAnotados();
        
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
