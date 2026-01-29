<?php

namespace Tests\integration\misas\infrastructure\repositories;

use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\misas\domain\entity\Plantilla;
use Tests\myTest;
use Tests\factories\misas\PlantillaFactory;

class PgPlantillaRepositoryTest extends myTest
{
    private PlantillaRepositoryInterface $repository;
    private PlantillaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PlantillaRepositoryInterface::class);
        $this->factory = new PlantillaFactory();
    }

    public function test_guardar_nuevo_plantilla()
    {
        // Crear instancia usando factory
        $oPlantilla = $this->factory->createSimple();
        $id = $oPlantilla->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oPlantilla);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oPlantillaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oPlantillaGuardado);
        $this->assertEquals($id, $oPlantillaGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oPlantillaGuardado);
    }

    public function test_actualizar_plantilla_existente()
    {
        // Crear y guardar instancia usando factory
        $oPlantilla = $this->factory->createSimple();
        $id = $oPlantilla->getId_item();
        $this->repository->Guardar($oPlantilla);

        // Crear otra instancia con datos diferentes para actualizar
        $oPlantillaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oPlantillaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oPlantillaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oPlantillaActualizado);

        // Limpiar
        $this->repository->Eliminar($oPlantillaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPlantilla = $this->factory->createSimple();
        $id = $oPlantilla->getId_item();
        $this->repository->Guardar($oPlantilla);

        // Buscar por ID
        $oPlantillaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oPlantillaEncontrado);
        $this->assertInstanceOf(Plantilla::class, $oPlantillaEncontrado);
        $this->assertEquals($id, $oPlantillaEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oPlantillaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPlantilla = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPlantilla);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPlantilla = $this->factory->createSimple();
        $id = $oPlantilla->getId_item();
        $this->repository->Guardar($oPlantilla);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oPlantillaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oPlantillaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_plantilla()
    {
        // Crear y guardar instancia usando factory
        $oPlantilla = $this->factory->createSimple();
        $id = $oPlantilla->getId_item();
        $this->repository->Guardar($oPlantilla);

        // Verificar que existe
        $oPlantillaExiste = $this->repository->findById($id);
        $this->assertNotNull($oPlantillaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oPlantillaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oPlantillaEliminado = $this->repository->findById($id);
        $this->assertNull($oPlantillaEliminado);
    }

    public function test_get_plantillas_sin_filtros()
    {
        $result = $this->repository->getPlantillas();
        
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
