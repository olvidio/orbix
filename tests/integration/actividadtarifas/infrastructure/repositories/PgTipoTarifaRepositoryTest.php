<?php

namespace Tests\integration\actividadtarifas\infrastructure\repositories;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use Tests\myTest;
use Tests\factories\actividadtarifas\TipoTarifaFactory;

class PgTipoTarifaRepositoryTest extends myTest
{
    private TipoTarifaRepositoryInterface $repository;
    private TipoTarifaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $this->factory = new TipoTarifaFactory();
    }

    public function test_guardar_nuevo_tipoTarifa()
    {
        // Crear instancia usando factory
        $oTipoTarifa = $this->factory->createSimple();
        $id = $oTipoTarifa->getId_tarifa();

        // Guardar
        $result = $this->repository->Guardar($oTipoTarifa);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTipoTarifaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTipoTarifaGuardado);
        $this->assertEquals($id, $oTipoTarifaGuardado->getId_tarifa());

        // Limpiar
        $this->repository->Eliminar($oTipoTarifaGuardado);
    }

    public function test_actualizar_tipoTarifa_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoTarifa = $this->factory->createSimple();
        $id = $oTipoTarifa->getId_tarifa();
        $this->repository->Guardar($oTipoTarifa);

        // Crear otra instancia con datos diferentes para actualizar
        $oTipoTarifaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTipoTarifaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTipoTarifaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTipoTarifaActualizado);

        // Limpiar
        $this->repository->Eliminar($oTipoTarifaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoTarifa = $this->factory->createSimple();
        $id = $oTipoTarifa->getId_tarifa();
        $this->repository->Guardar($oTipoTarifa);

        // Buscar por ID
        $oTipoTarifaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTipoTarifaEncontrado);
        $this->assertInstanceOf(TipoTarifa::class, $oTipoTarifaEncontrado);
        $this->assertEquals($id, $oTipoTarifaEncontrado->getId_tarifa());

        // Limpiar
        $this->repository->Eliminar($oTipoTarifaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTipoTarifa = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oTipoTarifa);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoTarifa = $this->factory->createSimple();
        $id = $oTipoTarifa->getId_tarifa();
        $this->repository->Guardar($oTipoTarifa);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_tarifa', $aDatos);
        $this->assertEquals($id, $aDatos['id_tarifa']);

        // Limpiar
        $oTipoTarifaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTipoTarifaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tipoTarifa()
    {
        // Crear y guardar instancia usando factory
        $oTipoTarifa = $this->factory->createSimple();
        $id = $oTipoTarifa->getId_tarifa();
        $this->repository->Guardar($oTipoTarifa);

        // Verificar que existe
        $oTipoTarifaExiste = $this->repository->findById($id);
        $this->assertNotNull($oTipoTarifaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTipoTarifaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTipoTarifaEliminado = $this->repository->findById($id);
        $this->assertNull($oTipoTarifaEliminado);
    }

    public function test_get_array_tipo_tarifas_sin_filtros()
    {
        $result = $this->repository->getArrayTipoTarifas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_tipo_tarifas_sin_filtros()
    {
        $result = $this->repository->getTipoTarifas();
        
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
