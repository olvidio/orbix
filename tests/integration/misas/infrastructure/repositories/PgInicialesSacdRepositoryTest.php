<?php

namespace Tests\integration\misas\infrastructure\repositories;

use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;
use Tests\myTest;
use Tests\factories\misas\InicialesSacdFactory;

class PgInicialesSacdRepositoryTest extends myTest
{
    private InicialesSacdRepositoryInterface $repository;
    private InicialesSacdFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(InicialesSacdRepositoryInterface::class);
        $this->factory = new InicialesSacdFactory();
    }

    public function test_guardar_nuevo_inicialesSacd()
    {
        // Crear instancia usando factory
        $oInicialesSacd = $this->factory->createSimple();
        $id = $oInicialesSacd->getId_nom();

        // Guardar
        $result = $this->repository->Guardar($oInicialesSacd);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oInicialesSacdGuardado = $this->repository->findById($id);
        $this->assertNotNull($oInicialesSacdGuardado);
        $this->assertEquals($id, $oInicialesSacdGuardado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oInicialesSacdGuardado);
    }

    public function test_actualizar_inicialesSacd_existente()
    {
        // Crear y guardar instancia usando factory
        $oInicialesSacd = $this->factory->createSimple();
        $id = $oInicialesSacd->getId_nom();
        $this->repository->Guardar($oInicialesSacd);

        // Crear otra instancia con datos diferentes para actualizar
        $oInicialesSacdUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oInicialesSacdUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oInicialesSacdActualizado = $this->repository->findById($id);
        $this->assertNotNull($oInicialesSacdActualizado);

        // Limpiar
        $this->repository->Eliminar($oInicialesSacdActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oInicialesSacd = $this->factory->createSimple();
        $id = $oInicialesSacd->getId_nom();
        $this->repository->Guardar($oInicialesSacd);

        // Buscar por ID
        $oInicialesSacdEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oInicialesSacdEncontrado);
        $this->assertInstanceOf(InicialesSacd::class, $oInicialesSacdEncontrado);
        $this->assertEquals($id, $oInicialesSacdEncontrado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oInicialesSacdEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oInicialesSacd = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oInicialesSacd);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oInicialesSacd = $this->factory->createSimple();
        $id = $oInicialesSacd->getId_nom();
        $this->repository->Guardar($oInicialesSacd);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_nom', $aDatos);
        $this->assertEquals($id, $aDatos['id_nom']);

        // Limpiar
        $oInicialesSacdParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oInicialesSacdParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_inicialesSacd()
    {
        // Crear y guardar instancia usando factory
        $oInicialesSacd = $this->factory->createSimple();
        $id = $oInicialesSacd->getId_nom();
        $this->repository->Guardar($oInicialesSacd);

        // Verificar que existe
        $oInicialesSacdExiste = $this->repository->findById($id);
        $this->assertNotNull($oInicialesSacdExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oInicialesSacdExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oInicialesSacdEliminado = $this->repository->findById($id);
        $this->assertNull($oInicialesSacdEliminado);
    }

    public function test_get_iniciales_sacd_sin_filtros()
    {
        $result = $this->repository->getInicialesSacd();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
