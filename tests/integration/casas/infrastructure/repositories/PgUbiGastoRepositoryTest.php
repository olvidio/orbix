<?php

namespace Tests\integration\casas\infrastructure\repositories;

use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\entity\UbiGasto;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use Tests\factories\casas\GrupoCasaFactory;
use Tests\factories\ubis\CasaFactory;
use Tests\myTest;
use Tests\factories\casas\UbiGastoFactory;

class PgUbiGastoRepositoryTest extends myTest
{
    private UbiGastoRepositoryInterface $repository;
    private UbiGastoFactory $factory;
    private CasaRepositoryInterface $casaRepository;
    private array $casasCreadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);
        $this->casaRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $this->factory = new UbiGastoFactory();

         // Crear las casas/ubis necesarias para las foreign keys
        $casaFactory = new CasaFactory();
        $casa1 = $casaFactory->createSimple(-10019001);

        $this->casaRepository->Guardar($casa1);

        $this->casasCreadas[] = $casa1;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->casasCreadas as $casa) {
            $this->casaRepository->Eliminar($casa);
        }
    }

    public function test_guardar_nuevo_ubiGasto()
    {
        // Crear instancia usando factory
        $oUbiGasto = $this->factory->createSimple();
        $id = $oUbiGasto->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oUbiGasto);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oUbiGastoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oUbiGastoGuardado);
        $this->assertEquals($id, $oUbiGastoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oUbiGastoGuardado);
    }

    public function test_actualizar_ubiGasto_existente()
    {
        // Crear y guardar instancia usando factory
        $oUbiGasto = $this->factory->createSimple();
        $id = $oUbiGasto->getId_item();
        $this->repository->Guardar($oUbiGasto);

        // Crear otra instancia con datos diferentes para actualizar
        $oUbiGastoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oUbiGastoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oUbiGastoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oUbiGastoActualizado);

        // Limpiar
        $this->repository->Eliminar($oUbiGastoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oUbiGasto = $this->factory->createSimple();
        $id = $oUbiGasto->getId_item();
        $this->repository->Guardar($oUbiGasto);

        // Buscar por ID
        $oUbiGastoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oUbiGastoEncontrado);
        $this->assertInstanceOf(UbiGasto::class, $oUbiGastoEncontrado);
        $this->assertEquals($id, $oUbiGastoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oUbiGastoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oUbiGasto = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oUbiGasto);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oUbiGasto = $this->factory->createSimple();
        $id = $oUbiGasto->getId_item();
        $this->repository->Guardar($oUbiGasto);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oUbiGastoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oUbiGastoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_ubiGasto()
    {
        // Crear y guardar instancia usando factory
        $oUbiGasto = $this->factory->createSimple();
        $id = $oUbiGasto->getId_item();
        $this->repository->Guardar($oUbiGasto);

        // Verificar que existe
        $oUbiGastoExiste = $this->repository->findById($id);
        $this->assertNotNull($oUbiGastoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oUbiGastoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oUbiGastoEliminado = $this->repository->findById($id);
        $this->assertNull($oUbiGastoEliminado);
    }

    public function test_get_ubis_gastos_sin_filtros()
    {
        $result = $this->repository->getUbisGastos();
        
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
